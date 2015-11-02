<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author Changwang <chenyongwang1104@163.com>
 * @copyright Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license http://www.cntysoft.com/license/new-bsd     New BSD License
*/
namespace Cntysoft\Framework\Pay\AliPay;

final class Constant
{
    //银行简码,混合渠道
    //对共账户转账
    const BANK_ICBCBTB = 'ICBCBTB'; //中国工商银行（B2B）
    const BANK_ABCBTB = 'ABCBTB'; //中国农业银行（B2B）
    const BANK_CCBBTB = 'CCBBTB'; //中国建设银行（B2B）
    const BANK_SPDBB2B = 'SPDBB2B'; //上海浦东发展银行（B2B）
    const BANK_BOCBTB = 'BOCBTB'; //中国银行（B2B）
    const BANK_CMBBTB = 'CMBBTB'; //招商银行（B2B）
    //对私账户转账
    const BANK_BOCB2C = 'BOCB2C'; //中国银行
    const BANK_ICBCB2C = 'ICBCB2C'; //中国工商银行
    const BANK_CMB = 'CMB'; //招商银行
    const BANK_CCB = 'CCB'; //中国建设银行
    const BANK_ABC = 'ABC'; //中国农业银行
    const BANK_SPDB = 'SPDB'; //上海浦东发展银行
    const BANK_CIB = 'CIB'; //兴业银行
    const BANK_GDB = 'GDB'; //广发银行
    const BANK_FDB = 'FDB'; //富滇银行
    const BANK_HZCBB2C = 'HZCBB2C'; //杭州银行
    const BANK_SHBANK = 'SHBANK'; //上海银行
    const BANK_NBBANK = 'NBBANK'; //宁波银行
    const BANK_SPABANK = 'SPABANK'; //平安银行
    const BANK_POSTGC = 'POSTGC'; //中国邮政储蓄银行
}