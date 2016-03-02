<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZaccountsView extends Model
{
    protected $table = "z_accounts_view";

    public $timestamps = false;

    protected $fillable = [
        'id', 'fb_id', 'u_bdate', 'bdate', 'u_name', 'u_fname', 'u_lname', 'u_mname', 'u_email', 'u_gender', 'u_regdate', 'login_source', 'login_provider', 'u_avatar', 'geo_country', 'activated', 'platform',
    ];

    public static function getUsersInAuthCinehost($condition, $orderBy, $limit, $offset)
    {
        $query = "SELECT z_accounts_view.*,sum(z_orders.amount) as uamount  FROM z_accounts_view
                 LEFT JOIN z_orders ON z_orders.user_id=z_accounts_view.id AND z_orders.status=1  AND  z_orders.test=0
                 WHERE  z_accounts_view.activated=1 $condition Group by z_accounts_view.id  $orderBy LIMIT ".$limit." OFFSET ".$offset;
        return self::hydrateRaw($query);
    }

    public static function getUsers($storeID, $condition, $orderBy, $limit, $offset)
    {
        $query = "SELECT z_accounts_view.*,sum(z_orders.amount) as uamount  FROM z_accounts_view
                 LEFT JOIN z_orders ON z_orders.user_id=z_accounts_view.id AND z_orders.status=1 AND  z_orders.test=0 AND z_orders.wl='".$storeID."'
                 WHERE z_accounts_view.login_source='".$storeID."' AND z_accounts_view.activated=1 $condition Group by z_accounts_view.id $orderBy LIMIT $limit OFFSET $offset" ;
        return self::hydrateRaw($query);
    }

    public static function getUsersTotalInAuthCinehost($condition)
    {
        $query = "SELECT COUNT(*) as count FROM z_accounts_view WHERE  z_accounts_view.activated=1 $condition  ";
        return self::hydrateRaw($query);
    }

    public static function getUsersTotal($storeID, $condition)
    {
        $query = "SELECT COUNT(*) as count FROM z_accounts_view WHERE z_accounts_view.login_source=".$storeID." AND z_accounts_view.activated=1 $condition  ";
        return self::hydrateRaw($query);
    }

}
