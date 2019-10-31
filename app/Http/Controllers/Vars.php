<?php
namespace App\Http\Controllers;


class Vars extends Controller
{
    
    public static $timezoneDefault = "America/New_York";
    
    public static $reportIds = array(
        ["title" => "Quantity and Average Time by Order",      "id" => "quantity_and_average_time_by_order"],
        ["title" => "Quantity and Average Time by Item",       "id" => "quantity_and_average_time_by_item"],
        ["title" => "Quantity and Average Time by Item Name",  "id" => "quantity_and_average_time_by_item_name"],
        ["title" => "Quantity and Average Time by Category",   "id" => "quantity_and_average_time_by_category"]
    );
    
}
?>