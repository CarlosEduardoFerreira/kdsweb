<?php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\URL;

    $me = Auth::User();
?>

@extends('layouts.welcome_first')
{{ Html::script(mix('assets/app/js/app.js')) }}
@tojs
{{ Html::script(mix('assets/admin/js/admin.js')) }}

@section('content')
    <?php
        // Are licenses set up for the reseller?
        $mainDB = env('DB_DATABASE', 'kdsweb');
        $result = DB::select("SELECT quantity 
                                FROM $mainDB.licenses_log 
                                WHERE store_guid IN (
                                                        SELECT DISTINCT store_guid 
                                                        FROM $mainDB.users
                                                        WHERE parent_id = ?
                                                        OR parent_id IN (
                                                                            SELECT DISTINCT id
                                                                            FROM $mainDB.users
                                                                            WHERE parent_id = ?
                                                                        )
                                                    )
                                ORDER BY update_time DESC
                                LIMIT 1", [$me->id, $me->id]);
        if (count($result) > 0) {
    ?>
            <div class="col-12">
                <embed id="embeddedPDF" src="<?= URL::to("/admin/resellers/{$me->id}/agreement") ?>" width="800px" height="400px" 
                        alt="pdf" pluginspage="http://www.adobe.com/products/acrobat/readstep2.html">
            </div> 
            <BR> 
            <div class="">
                <form action='<?= URL::to("/admin/resellers/confirm_agreement") ?>' method='POST'>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="switch">
                            <input type="checkbox" id="agree" name="agree" value="ok">
                            <span class="slider round" ></span>
                        </label>
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="active">
                            By checking this box, you agree with the Reseller Subscription Agreement.
                        </label>  
                    </div>
                    <BR>
                    <button type='submit' class="btn btn-success" id="continue">Continue</button>
                </form>
            </div>
    <?php
        } else {
    ?>
    Stores have not been set up yet. Please contact the reseller.
    <?php
        }
    ?>
@endsection