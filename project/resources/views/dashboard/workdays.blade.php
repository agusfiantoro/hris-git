<style type="text/css">
    .big_page{
        position: absolute;top: 0;left: 0;width: 100%;height: 100%;
    }
    .attendanceDay{
        font-size: 18px;font-weight: bold;text-align: center;
    }
    .attendanceTime{
        font-size: 20px;font-weight: bold;
    }
    .box-img-header{
        width: 40px;height: 40px;border-radius: 200px;background-color: #a5b0b7;overflow: hidden;
    }
    .img-header{
        touch-action: pan-y; user-select: none; -webkit-user-drag: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0);width: 100%;height: 100%;object-fit: cover;object-position: top;
    }
    .box-img{
        width: 40px;height: 40px;border-radius: 200px;background-color: #a5b0b7;overflow: hidden;display: block;margin-left: auto;margin-right: auto;margin-top: 10px;
    }
    @media (max-width: 483px) {
        #webcamContainer video {
            /*max-width: 100%;max-height: 100%;*/
            /* margin-left: 40%;; */
        }
        #webcamContainer img {
            /*max-width: 100%;max-height: 100%;*/
            /* margin-left: 40%;; */
        }
    }
    @media (max-width: 741px) {
        #webcamContainer video {
            /*max-width: 100%;max-height: 100%;*/
            /* margin-left: 40%;; */
        }
        #webcamContainer img {
            /*max-width: 100%;max-height: 100%;*/
            /* margin-left: 40%;; */
        }
    }
</style>
<table class="table">
    <tr>
        <td>
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col-3 ">
                            <b>Name</b>
                        </div>
                        <div class="col-9 ">
                            <b> : <?= (@$attendance[0]->name) ?></b>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <b>Position</b>
                        </div>
                        <div class="col-9">
                            <b> : <?= (@$attendance[0]->position_routing) ?></b>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <b>Location</b>
                        </div>
                        <div class="col-9">
                            <b> : {{(@$attendance[0]->location)}}</b>
                        </div>
                    </div>
                    <!-- <div class="row">
                        <div class="col-3">
                            <b>Office Hour</b>
                        </div>
                        <div class="col-9">
                            <b> : <?= (@$attendance[0]->office_hour) ?> <?= (@$attendance[0]->schedule_employee_timezone) ?></b>
                        </div>
                    </div> -->
                </div>
            </div>
        </td>
    </tr>
</table>

<table class="table table-bordered table-dashed">
    <tr>
        <th>
            <div class="row">
                <div class="col-md-12">
                    <center class="attendanceDay">
                        <span class="text-primary"><b>Today</b></span></br>
                        <?php if(@$attendance[0]->current_dates != null){
                            echo date('l, d F Y', strtotime(@$attendance[0]->current_dates));
                        } ?>
                    </center>
                </div>
            </div>
        </th>
    </tr>
    <tr>
        <td>
            <div class="row">
                <div class="col-2">
                    <div class="img-box box-img">
                        <?php if(!is_null(@$attendance[0]->image_attachment_in)){
                            echo '<center><img style="width:100%;" src="'.url('project/storage/app/public/images/'.@$attendance[0]->image_attachment_in).'" alt=""></center>';
                        } ?>
                    </div>
                </div>
                <div class="col-3">
                    <center><span class="attendanceLabel">Start Time</span>
                    <div class="row">
                        <div class="col-md-12">
                            <span class="attendanceTime text-success">
                            <?php if(@$attendance[0]->actual_time_in != null){
                                echo date('H:i', strtotime(@$attendance[0]->actual_time_in));
                            } else if(@$attendance[0]->actual_time_in == null){
                                echo '-- : --';
                            } ?>
                            </span>
                        </div>
                    </div>
                    <span class="attendanceLabel"><?= (@$attendance[0]->current_employee_timezone)?></span></center>
                </div>
                <div class="col-3">
                    <div class="img-box box-img" >
                        <?php if(!is_null(@$attendance[0]->image_attachment_out)){
                            echo '<center><img style="width:100%;" src="'.url('project/storage/app/public/images/'.@$attendance[0]->image_attachment_out).'" alt=""></center>';
                        } ?>
                    </div>
                </div>
                <div class="col-4">
                    <center><span class="attendanceLabel">End Time</span>

                    <div class="row">
                        <div class="col-md-12">
                            <span class="attendanceTime text-danger">
                            <?php if(@$attendance[0]->actual_time_out != null){
                                echo date('H:i', strtotime(@$attendance[0]->actual_time_out));
                            } else if(@$attendance[0]->actual_time_out == null){
                                echo '-- : --';
                            } ?>
                            </span>
                        </div>
                    </div>
                    <span class="attendanceLabel"><?= (@$attendance[0]->current_employee_timezone)?></span></center>
                </div>
            </div>
        </td>
    </tr>
</table>

<table class="table table-bordered table-dashed">
    <tr>
        <th>
            <div class="row">
                <div class="col-md-12">
                    <center class="attendanceDay">
                        <span class="text-primary"><b>History</b></span>
                    </center>
                </div>
            </div>
        </th>
    </tr>
    
    <?php if(!is_null(@$attendance)) {for ($i = 1; $i < count(@$attendance); $i++){ ?>
    <tr>
        <td>
            <div class="row">
                <div class="col-md-12">
                    <center class="attendanceDay">
                        <?php 
                        if(@$attendance[$i]->schedule_time_in != null){
                            echo date('l, d F Y', strtotime(@$attendance[$i]->schedule_time_in));
                        } else {
                            echo '-';
                        } ?>
                    </center>
                </div>
                <div class="col-2">
                    <div class="img-box box-img" >
                        <?php if(!is_null(@$attendance[$i]->image_attachment_in)){
                            echo '<center><img style="width:100%;" src="'.url('project/storage/app/public/images/'.@$attendance[$i]->image_attachment_in).'" alt=""></center>';
                        } ?>
                    </div>
                </div>
                <div class="col-3">
                    <center><span class="attendanceLabel">Start Time</span>

                    <div class="row">
                        <div class="col-md-12">
                            <span class="attendanceTime text-success">
                                <?php if(@$attendance[$i]->actual_time_in != null){
                                    echo date('H:i', strtotime(@$attendance[$i]->actual_time_in));
                                } else if(@$attendance[$i]->actual_time_in == null){
                                    echo '-- : --';
                                } ?>
                            </span>
                        </div>
                    </div>
                    <span class="attendanceLabel"><?= (@$attendance[$i]->current_employee_timezone)?></span></center>
                </div>
                <div class="col-3">
                    <div class="img-box box-img">
                        <?php if(!is_null(@$attendance[$i]->image_attachment_out)){
                            echo '<center><img style="width:100%;" src="'.url('project/storage/app/public/images/'.@$attendance[$i]->image_attachment_out).'" alt=""></center>';
                        } ?>
                    </div>
                </div>
                <div class="col-4">
                    <center><span class="attendanceLabel">End Time</span>

                    <div class="row">
                        <div class="col-md-12">
                            <span class="attendanceTime text-danger">
                                <?php if(@$attendance[$i]->actual_time_out != null){
                                    echo date('H:i', strtotime(@$attendance[$i]->actual_time_out));
                                } else if(@$attendance[$i]->actual_time_out == null){
                                    echo '-- : --';
                                } ?>
                            </span>
                        </div>
                    </div>
                    <span class="attendanceLabel"><?= (@$attendance[$i]->current_employee_timezone)?></span></center>
                </div>
            </div>
        </td>
    </tr>
    <?php }} ?>
</table>

