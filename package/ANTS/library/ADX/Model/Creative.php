<?php
/**
 * Created by PhpStorm.
 * User: Hi
 * Date: 11/04/2016
 * Time: 11:24 AM
 */

namespace ADX\Model;

use ADX\Entity;
use ADX\DAO;
use ADX\Business;

class Creative extends Entity\Creative
{
    const CREATIVE_PAYMENT_MODEL_CPC = 1;
    const CREATIVE_PAYMENT_MODEL_CPM = 2;//viewable
    const CREATIVE_PAYMENT_MODEL_CPI = 3;
    const CREATIVE_PAYMENT_MODEL_CPD = 4;
    const CREATIVE_PAYMENT_MODEL_CPMI = 5;//impression

}