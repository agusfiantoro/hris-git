<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class AssetStats {
    
    public static function totalMonthlyPurchasedAssets($idCompany) {
        return DB::select("SELECT 
                            to_char(to_timestamp (month::text, 'MM'), 'Month') AS month_text,
                            *
                        FROM (
                            select 
                                left(right(asset_number,12),4) as year,
                                left(right(asset_number,7),2) as month, 
                                sum(original_cost) as total_purchase_asset_permonth
                            from asset.sp_funct_asset_view (NULL,?,null,null,null,null,null,null,null,null,null,null)
                            group by left(right(asset_number,12),4),left(right(asset_number,7),2)
                            order by left(right(asset_number,12),4) asc,left(right(asset_number,7),2) ASC
                        ) funct", 
                        [$idCompany]);
    }

    public static function totalAnnuallyPurchasedAssets($idCompany) {
        return DB::select("SELECT 
                            left(right(asset_number,12),4) as year, 
                            sum(original_cost) as total_purchase_asset_peryear
                        from asset.sp_funct_asset_view (NULL,?,null,null,null,null,null,null,null,null,null,null)
                        group by left(right(asset_number,12),4)
                        order by left(right(asset_number,12),4) ASC", 
                        [$idCompany]);
    }

    public static function totalAssetValue($idCompany) {
        return DB::selectOne("SELECT sum(original_cost) as total_asset_value
                        from asset.sp_funct_asset_view (NULL,?,null,null,null,null,null,null,null,null,null,null)",
                        [$idCompany])->total_asset_value;
    }

    public static function totalNetBookByGroup($idCompany) {
        return DB::select("SELECT asset_group, sum(current_cost) as total_netbook_asset_value
                        from asset.sp_funct_asset_view (NULL,?,null,null,null,null,null,null,null,null,null,null)
                        group by asset_group", [$idCompany]);
    }

    public static function totalMonthlyAssetQuantity($idCompany) {
        return DB::select("SELECT asset_group, left(right(asset_number,12),4) as year, sum(current_units) as total_quantity_asset_permonth
                        from asset.sp_funct_asset_view (NULL,?,null,null,null,null,null,null,null,null,null,null)
                        group by left(right(asset_number,12),4), asset_group
                        order by left(right(asset_number,12),4) asc", 
                        [$idCompany]);
    }

    public static function annualDepreciationValue($idCompany) {
        return DB::select("SELECT to_char(depreciation_date,'YYYY') as depreciation_year, sum(depreciation_value) as depreciation_value_per_year
                        from asset.sp_funct_depreciation_detail_view (?,null,True,null,null)
                        group by to_char(depreciation_date,'YYYY')
                        order by to_char(depreciation_date,'YYYY') asc",
                        [$idCompany]);
    }

    public static function monthlyDepreciationValue($idCompany) {
        return DB::select("SELECT to_char(depreciation_date,'YYYY-MON') as month, sum(depreciation_value) as depreciation_value_per_month
                        from asset.sp_funct_depreciation_detail_view (?,null,True,null,null)
                        group by depreciation_date
                        order by depreciation_date asc",
                        [$idCompany]);
    }

    public static function depreciationByGroup($idCompany) {
        return DB::select("SELECT asset_group, sum(depreciation_value) as depreciation_value_per_year
                        from asset.sp_funct_depreciation_detail_view (?,null,True,null,null)
                        group by asset_group
                        ORDER BY depreciation_value_per_year desc",
                        [$idCompany]);
    }

    public static function totalOriginalCostByGroup($idCompany) {
        return DB::select("SELECT asset_group, sum(original_cost) as total_original_cost_asset_pergroup
                        from asset.sp_funct_asset_view (NULL,?,null,null,null,null,null,null,null,null,null,null)
                        group by asset_group", 
                        [$idCompany]);
    }

    public static function totalOutstandingAssetsByGroup($idCompany) {
        return DB::select("select asset_group, queue_process_status, count(id_asset)
                        from asset.sp_funct_asset_view (NULL,?,null,null,null,null,null,null,null,null,null,null)
                        where queue_process_status = 'New'
                        group by asset_group, queue_process_status",
                        [$idCompany]);
    }

    public static function totalAssetByGroupAndBranch($idCompany) {
        return DB::select("select asset_group, branch, count(id_asset)
                        from asset.sp_funct_asset_view (NULL,?,null,null,null,null,null,null,null,null,null,null)
                        group by asset_group, branch",
                        [$idCompany]);
    }

    public static function totalAssetQuantityByGroup($idCompany) {
        return DB::select("select asset_group, sum(current_units) as total_quantity_asset_pergroup
                        from asset.sp_funct_asset_view (NULL,?,null,null,null,null,null,null,null,null,null,null)
                        group by asset_group",
                        [$idCompany]);
    }
}