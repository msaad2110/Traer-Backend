<?php

/**
 * App related helper functions.
 * Created by Ali Anwar on 29/07/2020
 */

namespace App\Helpers;

/**
 * Class AppGlobals
 * @package App
 *
 * A class to hold global flags for app special functions.
 */
class AppGlobals
{
    /**
     * @var User The current user object made available from Authenticate Middleware.
     */
    private static $__user = null;

    /**
     * @var bool Send authenticated user data in the next wt_api_json_success() call.
     */
    private static $__send_auth_user_data = false;

    /**
     * @var int Save selected company id from auth middleware to be used in auth user data sending.
     */
    private static $__company_id = 0;

    /**
     * @var int Save selected branch id from auth middleware to be used in auth user data sending.
     */
    private static $__branch_id = 0;

    /**
     * @var int Save selected fiscal year id from auth middleware to be used in auth user data sending.
     */
    private static $__fiscal_year_id = 0;

    /**
     * @return User
     */
    public static function getUser()
    {
        return self::$__user;
    }

    /**
     * Get current user id and if its not set then return a default value.
     * 
     * @param int $default
     * 
     * @return int
     */
    public static function getUserId($default = 0)
    {
        if (is_null(self::$__user)) {
            return $default;
        }

        return self::$__user->id;
    }

    /**
     * @param User $_user
     */
    public static function setUser($_user)
    {
        self::$__user = $_user;
    }

    /**
     * @return bool
     */
    public static function isSendAuthUserData()
    {
        return self::$__send_auth_user_data;
    }

    /**
     * @param bool $_send_auth_user_data
     */
    public static function setSendAuthUserData($_send_auth_user_data)
    {
        self::$__send_auth_user_data = $_send_auth_user_data;
    }

    /**
     * @return int
     */
    public static function getCompanyId()
    {
        return self::$__company_id;
    }

    /**
     * @param int $_company_id
     */
    public static function setCompanyId($_company_id)
    {
        self::$__company_id = $_company_id;
    }

    /**
     * @return int
     */
    public static function getBranchId()
    {
        return self::$__branch_id;
    }

    /**
     * @param int $_branch_id
     */
    public static function setBranchId($_branch_id)
    {
        self::$__branch_id = $_branch_id;
    }

    /**
     * @return int
     */
    public static function getFiscalYearId()
    {
        return self::$__fiscal_year_id;
    }

    /**
     * @param int $__fiscal_year_id
     */
    public static function setFiscalYearId($__fiscal_year_id)
    {
        self::$__fiscal_year_id = $__fiscal_year_id;
    }
}
