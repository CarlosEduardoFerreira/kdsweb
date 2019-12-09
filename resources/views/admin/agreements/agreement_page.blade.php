
@extends('admin.layouts.admin')

@section('content')

<style>

table
{ 
  font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 700px;
}
</style>
<?php
$useremail = Auth::user()->email;
$usersTable = DB::select( "SELECT * from agreement_acceptance WHERE email = ?", [$useremail]);
$row = sizeof($usersTable);


$id = Auth::user()->id;
$ip = Request::ip();
$page = 10;
$time = DB::select("SELECT * FROM  kdsweb.agreement_acceptance WHERE email = ?", [$useremail]);


echo "<BR><table>";
                            echo "<tr ><th id=ty;>Date Signed</th><th>Agreement PDF</th></tr>";
                            foreach($time as $t)
                            {
                                $sig = sprintf("%08d", $id) . sprintf("%04d", $page) . md5($id . $ip . $page . $t->accepted_at);
                                echo "<tr><td>";
                                $t->accepted_at;
                                $date = new DateTime("@$t->accepted_at");
                                echo $date->format('d F Y') . "\n";
                                echo "</td><td>";
                                echo "<a href='".URL::to("./agreements/$sig.pdf")."' target='_blank'>Download</a>";
                                echo "</td></tr>";
                            }
                            echo "</table>";
                            ?>
                            
<style>

table
{
  text-align: justify !important;
  text-align-last: left !important;
  text-align: left !important;
  margin: left !important;
  align-content: left !important;  
  font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 700px;
}

tr{
  border: 1px solid #ddd;
  padding: 8px;
}


th{
  padding-top: 12px;
  padding-bottom: 12px;
  padding-left:4px;
  text-align: left;
  background-color: #2A3F54;
  color: white;
}

td{
    padding-top: 4px;
    padding-bottom: 4px;
    padding-left: 4px;
}

}
</style>
@endsection