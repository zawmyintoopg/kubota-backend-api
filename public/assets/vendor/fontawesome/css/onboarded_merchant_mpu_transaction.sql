set colsep ','     -- separate columns with a comma
set pagesize 0   -- No header rows
set trimspool on -- remove trailing blanks
set headsep off  -- this may or may not be useful...depends on your headings.
set linesize 8000
SET AUTOPRINT ON;
col spoolname new_value spoolname;
col ydate new_value ydate;
--select to_char(sysdate-1, 'YYYY_Mon_DD DDMonYYYY')||'.csv' spoolname from dual;
select to_char(sysdate-1, 'YYYYMMDD')||'.csv' spoolname from dual;
select to_char(sysdate-1, 'YYYY-MM-DD') ydate from dual;
--spool D:\SCHEDULAR_LOCATION\For_Smile_Team\onboarded-merchant-list\wave_merchant_onboarding&spoolname;
--spool D:\SCHEDULAR_LOCATION\For_Smile_Team\ftp\root\smile_data_upload\merchant-onboard-list\&ydate\wave_merchant_onboarding&spoolname;
spool \\reportfiles.yomabank.org\ServiceMountPoints\WAVE\DMM-Merchant-Data-Upload\mpu_transaction_report\&ydate\mpu_transaction_report_&spoolname;
SET FEEDBACK OFF
select 'RECORDID,MERCHANTNAME,MERCHANT_SEGMENT,MERCHANTID,TERMINALID,MERCHANT_ACCOUNT_NUMBER,CARDCATEGORY,CARDTYPE,CARDNUMBER,TRANSACTIONTYPE,TRANSACTIONDATE,TRANSACTIONTIME,SETTLEMENTDATE,APPROVALCODE,REFERENCENUMBER,MDR_RATE,TRANSACTIONAMOUNT,MDRFEE,NETAMOUNT' from dual
union all
select '"'|| RECORDID ||'","' || MERCHANTNAME || '","' || MERCHANT_SEGMENT || '","' || MERCHANTID || '","' || TERMINALID || '","' || MERCHANT_ACCOUNT_NUMBER || '","' || CARDCATEGORY || '","' || CARDTYPE || '","' || CARDNUMBER || '","' || TRANSACTIONTYPE || '","'|| TRANSACTIONDATE  ||  '","' || TRANSACTIONTIME ||'","' || SETTLEMENTDATE ||'","' || APPROVALCODE ||'","' || REFERENCENUMBER ||'","' || MDR_RATE ||'","' || TRANSACTIONAMOUNT ||'","' || MDRFEE ||'","' || NETAMOUNT || '"'  
from (
select
cast(rownum as varchar(10)) RecordID,Merc.default_name as MerchantName,
(SELECT CASE WHEN d.field_value = 'MSEG0001' THEN 'Micro Merchant'
            WHEN d.field_value = 'MSEG0002' THEN 'Small Merchant'
            WHEN d.field_value = 'MSEG0003' THEN 'Standalone Merchant'
            WHEN d.field_value = 'MSEG0004' THEN 'Chain Stores'
            WHEN d.field_value = 'MSEG0005' THEN 'Corporates'
            WHEN d.field_value = 'MSEG0006' THEN 'Distributor'
        END AS MERCHANT_SEGMENT
     FROM main.acq_merchant m1 
     LEFT JOIN MAIN.com_flexible_data d ON d.object_id = m1.id
     LEFT JOIN MAIN.com_flexible_field f ON f.id = d.field_id
     WHERE d.field_id = 70000019
     AND m1.merchant_number = merc.identifier) AS MERCHANT_SEGMENT,
   to_number(Merc.identifier) AS MerchantID,
   T.identifier AS TerminalID,
   to_number(a.account_number) AS Merchant_Account_Number,
   case when Card.issuer_name = 'YOMA' then 'OnUs'
        else 'OffUs' end as CardCategory,
    Card.card_product as  CardType,
    oper.card_number as CardNumber,
     decode(die.value,
             'Purchase','Sale',
             'Purchase Void','Void',
             '','QR Payment',
             die.value
       ) AS TransactionType,
       TO_CHAR(Oper.datetime,'YYYY-MM-DD') AS TransactionDate,
       TO_CHAR(Oper.datetime,'hh24:mi:ss') AS TransactionTime,
       TO_CHAR(Oper.settlement_date,'YYYY-MM-DD') AS SettlementDate,
       decode(Oper.authz_code,
             '','-',
             Oper.authz_code
       ) AS ApprovalCode,
       Oper.ref AS ReferenceNumber,
       TO_CHAR(NVL(Oper.fee_amount / Oper.amount,0.00)*100,'fm9999999999999999999990D00') AS MDR_RATE,       
       TO_CHAR(Oper.amount,'fm9999999999999999999990D00') AS TransactionAmount,
       TO_CHAR(NVL(Oper.fee_amount,0),'fm9999999999999999999990D00') AS MDRFee,
       TO_CHAR(Oper.net_amount,'fm9999999999999999999990D00') AS NetAmount
 from svmp.T_PRC_OPER@DBLK_BO_MP oper inner join SVMP.T_MERCHANT_UNIT@DBLK_BO_MP MERC ON MERC.ID = Oper.Merchant_id inner join svmp.T_TERMINAL@DBLK_BO_MP T ON Oper.Terminal_ID = T.ID inner join main.acc_account_object ACJ ON ACJ.object_id = merc.external_id and entity_type = 'ENTTMRCH' inner join main.acc_account a ON a.id = ACJ.ACCOUNT_ID and a.status = 'ACSTACTV' LEFT JOIN (SELECT DISTINCT SUBSTR(PAN_BIN,1,6) AS pan_bin,issuer_name,card_type,card_product FROM MAIN.MPU_BIN_RANGE mb LEFT JOIN SVMP.T_PRC_OPER@DBLK_BO_MP Tmp ON SUBSTR (mb.PAN_BIN,1,6) = SUBSTR (Tmp.card_number,1,6)) Card ON SUBSTR (Card.PAN_BIN,1,6) = SUBSTR (Oper.card_number,1,6) LEFT JOIN SVMP.T_DICT_ITEM_ENTRY@DBLK_BO_MP die ON die.item_id = oper.type_id where oper.datetime between To_Date(trunc(sysdate-2) + 23/24) and To_Date(trunc(sysdate-1) + 23/24) and oper.card_type_id = 590 and 
ref not in (select ref from svmp.T_PRC_OPER@DBLK_BO_MP where datetime between To_Date(trunc(sysdate-2) + 23/24) and To_Date(trunc(sysdate-1) + 23/24) and card_type_id = 590 group by ref having count(*) > 1) and reversal = 0
);
spool off
