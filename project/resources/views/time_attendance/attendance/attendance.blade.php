    <div class="modal fade" id="location"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button onclick="refreshMap()" href="javascript:;" class="col-md-2 fa fa-refresh btn btn-success"></button>
                    <button class="close" data-dismiss="modal" aria-label="Close" style="font-size:16px; padding-left: 150px;">
                        <i class="nav-icon fas fa-close" data-dismiss="modal" style="cursor: pointer;"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <center><div id="mapContainer" style="width:100%;height:350px;"></div></center>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-md-12">
                        <center>
                            <span class="gps text-red">Please Allow Location / Enable GPS</span><br>
                            <button id="btnLocation" class="btn btn-primary" disabled>Confirm Location</button>
                            <button id="btnLocation_loading" class="btn btn-primary" style="display:none;">
                                <i class="fa fa-spinner fa-pulse"></i>
                            </button>
                        </center>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="snapshoot"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 text-right" style="font-size:16px">
                            <i class="nav-icon fas fa-close close_camera" data-dismiss="modal" style="cursor: pointer"></i>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <center><div id="webcamContainer" class="webcamContainer"></div></center>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-md-12">
                        <center>
                            <span class="camera text-red">Please allow camera access</span><br>
                            <button id="btnCapture" class="btn btn-primary" disabled>Take a Snapshot</button>
                        </center>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="dialogConfirmSave"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 text-right" style="font-size:16px">
                            <i class="nav-icon fas fa-close close_camera" data-dismiss="modal" style="cursor: pointer"></i>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <center>
                                <img id="avatar" class="rounded" style="width: 180px; height: 220px;" src="">
                            </center>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-center" style="padding-top:10px;">
                            <h4><?= (@$attendance[0]->name) ?></h4>
                            <h5 style="color:red;"><?= (@$attendance[0]->position_routing) ?></h5>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 text-center">
                            <h4><span id="shift_name"></span> <span id="shift_" style="display:none;"></span> 
                            <span class="attendanceTime text-green" id="dialogConfirmSave_date"></span></h4>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-md-12">
                        <center>
                            <button id="btnSaveAttendance" class="btn btn-primary">
                                Save Attendance
                            </button>
                            <button id="btnSaveAttendance_loading" class="btn btn-primary" style="display:none;">
                                <i class="fa fa-spinner fa-pulse"></i>
                            </button>
                        </center>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="dialogConfirmUpdate"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content p-3 p-md-4 p-lg-5" >
                <div class="container">
                    <div class="row">
                        <div class="col-12 align-items-center">
                            <center><h2>You have recorded your end time.</h2></center>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 align-items-center">
                            <center><h2>Do you want to update it?</h2></center>
                        </div>
                    </div>
                    <div class="row mt-12" style="padding-top:100px;">
                        <div class="col-6 align-items-center">
                            <center>
                                <button class="btn btn-danger" data-dismiss="modal" >Cancel</button>
                            </center>
                        </div>
                        <div class="col-6 align-items-center">
                            <center><button onclick="confirmUpdate()" class="btn btn-primary">
                                Update time
                            </button></center>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.css' rel='stylesheet' />
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.js'></script>
    <script type="text/javascript" src="<?= asset('vendor/webcam/webcam.js') ?>"></script>
    <script type="text/javascript">
        var img                 = null;
        var lat                 = 0;
        var lng                 = 0;
        let step_now            = 0;
        let mode                = 'online';
        let mapboxAccessToken   = "<?= $mapboxToken ?>";
        let zone                = "<?= @$attendance[0]->schedule_employee_timezone ?>".replace(/\s/g, '');
        let lock_location       = "<?= @$attendance[0]->lock_gps_location==true ? 1 : 0 ?>";
        let map_place           = '';
        let map_timezone        = convertZone(zone);
        let attendance_time     = '';
        var shift_name          = '';
        let server_hour_now     = 0;
        let server_minute_now   = 0;
        let getDistance         = 0;

        if(accessThisMenu.can_create == false){
            //variabel accessThisMenu ada di vendor/adminlte/master.blade
            $("#btnRecord").attr('disabled', true);
        }

        const checkConnection = async () => {
            const result = await window.navigator.onLine ? 'on' : 'off' ;
            return result;
        }

        const nextStep = (step_sekarang) => {
            checkConnection().then(res => {
                if(res=='on'){
                    if(step_sekarang==0){ modeOnline(1) }
                    if(step_sekarang==1){ modeOnline(2) }
                    if(step_sekarang==2){ modeOnline(3) }
                } else {
                    if(step_sekarang==0){ modeOffline(1) }
                    if(step_sekarang==1){ modeOffline(2) }
                    if(step_sekarang==2){ modeOffline(2) }
                }
            });
        }

        function modeOffline(step) {
            if(step == 1){
                step_now    = step;
                $("#snapshoot").modal('show');
                $("#location").modal('hide');
                $('#dialogConfirmSave').modal('hide');
                $('#dialogConfirmUpdate').modal('hide');
                showCam();
            } else if(step == 2){
                step_now    = step;
                checkoutAttendance(zone);
                $("#snapshoot").modal('hide');
                $("#location").modal('hide');
                $('#dialogConfirmSave').modal('show');
                $('#dialogConfirmUpdate').modal('hide');
                $("#btnLocation").show();
                $("#btnLocation_loading").hide();
            } 
        }

        function modeOnline(step) {
            if(step == 1){
                step_now    = step;
                drawMap();
                $("#snapshoot").modal('hide');
                $("#location").modal('show');
                $('#dialogConfirmSave').modal('hide');
                $('#dialogConfirmUpdate').modal('hide');
            } else if(step == 2){
                step_now    = step;
                showCam();
                $("#snapshoot").modal('show');
                $("#location").modal('hide');
                $('#dialogConfirmSave').modal('hide');
                $('#dialogConfirmUpdate').modal('hide');
                $("#btnLocation_loading").hide();
                $("#btnLocation").show();
            } else if(step == 3){
                step_now    = step;
                checkoutAttendance(zone);
                $("#snapshoot").modal('hide');
                $("#location").modal('hide');
                $('#dialogConfirmSave').modal('show');
                $('#dialogConfirmUpdate').modal('hide');
                $("#btnLocation").show();
                $("#btnLocation_loading").hide();
            } 
        }

        function confirmUpdate() {
            checkConnection().then(res => {
                if(res=='on'){
                    modeOnline(1)
                } else {
                    modeOffline(1)
                }
            });
        }

        function showCam() {
            Webcam.set({
                width: 250,
                height: 300,
                dest_width: 490,
                dest_height: 471,
                image_format: "jpeg",
                jpeg_quality: 90,
                force_flash: false,
                flip_horiz: true,
                fps: 45,
            });
            Webcam.set("constraints", {
                optional: [{ minWidth: 0 }]
            });
            Webcam.on('live', function () {
                $(".camera").hide()
                $("#btnCapture").prop('disabled', false);
            });
            Webcam.attach('#webcamContainer');
        }

        function getDistanceFromLatLon(lat1, lon1, lat2, lon2, unit) {
            if ((lat1 == lat2) && (lon1 == lon2)) {
                return 0;
            }
            else {
                var radlat1 = Math.PI * lat1/180;
                var radlat2 = Math.PI * lat2/180;
                var theta = lon1-lon2;
                var radtheta = Math.PI * theta/180;
                var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
                if (dist > 1) {
                    dist = 1;
                }
                dist = Math.acos(dist);
                dist = dist * 180/Math.PI;
                dist = dist * 60 * 1.1515;
                if (unit=="K") { dist = dist * 1.609344 } // Kilometer
                if (unit=="Meters") { dist = (dist * 1.609344) * 1000 } // Kilometer
                if (unit=="N") { dist = dist * 0.8684 }
                return dist; //default if unit == '' is miles
            }
        }

        function drawMap() {
            lng_borwita_pusat = 112.6963636;
            lat_borwita_pusat = -7.3537962;

            lng_marker = lng_borwita_pusat;
            lat_marker = lat_borwita_pusat;

            mapboxgl.accessToken = mapboxAccessToken;
            var map = new mapboxgl.Map({
                container: 'mapContainer',
                style: 'mapbox://styles/mapbox/streets-v11',
                center: [116.816356, -2.116069], // center adalah cakupan Indonesia
                zoom: 4,
                interactive: true,
            });
            var geolocate = new mapboxgl.GeolocateControl({
                positionOptions: {enableHighAccuracy: true},
                showAccuracyCircle: true,
            })
    
            geolocate.on('geolocate', function (userlocation) {
                lat = userlocation.coords.latitude;
                lng = userlocation.coords.longitude;
                radiusInMeters = 250;
                radiusInKilometers = radiusInMeters / 1000;

                map.flyTo({center:[lng, lat], zoom:14 });  //set zoom

                let location_name = "<?= @$attendance[0]->address_location ?? 'BORWITA'; ?>";
                const lat_work = "<?= @$attendance[0]->lat_loc ?? ''; ?>";
                const lng_work = "<?= @$attendance[0]->lng_loc ?? ''; ?>";

                if(lat_work == '' || lng_work == ''){
                    location_name = 'BORWITA HEAD OFFICE';
                }
                lat_marker = lat_work == '' ? lat_borwita_pusat : lat_work;
                lng_marker = lng_work == '' ? lng_borwita_pusat : lng_work;


                // ===== UNTUK MENAMPILKAN MARKER LOKASI BORWITA BY USER WORK LOCATION =====
                const marker_location = new mapboxgl.Marker({color:'#fe4909'})
                    .setLngLat([lng_marker, lat_marker])
                    .setPopup(new mapboxgl.Popup({offset: 25})
                    .setHTML(`<br><p class="text-bold">${location_name}</p>`))
                    .addTo(map);

                // ===== UNTUK MENAMPILKAN RADIUS =====
                map.addSource("polygon", {
                    "type": "geojson",
                    "data": {
                        "type": "FeatureCollection",
                        "features": [{
                            "type": "Feature",
                            "geometry": {"type": "Point", "coordinates": [lng_marker, lat_marker]}
                        }]
                    }
                });
                map.addLayer({
                    "id": "polygon",
                    "type": "circle",
                    "source": "polygon",
                    "paint": {
                        "circle-radius": {
                            stops: [ [0, 0], [20, metersToPixelsAtMaxZoom(radiusInMeters, lat_marker)] ], base: 2
                        },
                        "circle-color": "#223b53",
                        "circle-opacity": 0.6
                    }
                });
                // ===================================

                if(lock_location=='1'){
                    getDistance = getDistanceFromLatLon(lat_marker, lng_marker, lat, lng, 'Meters');
                    if(getDistance > radiusInMeters){
                        //Lokasi absen diluar radius
                        $(".gps").html('Your Location is out of area');
                        $(".gps").show();
                        $("#btnLocation").prop('disabled', true);
                    } else {
                        //Lokasi absen didalam radius
                        $(".gps").html('Please Allow Location / Enable GPS');
                        $(".gps").hide();
                        $("#btnLocation").prop('disabled', false);
                    }
                } else {
                    //kondisi normal sudah mendapatkan location
                    $(".gps").html('Please Allow Location / Enable GPS');
                    $(".gps").hide();
                    $("#btnLocation").prop('disabled', false);
                }
            });

            map.on('load', () => {
                map.resize();
                geolocate.trigger();
            });

            const metersToPixelsAtMaxZoom = (meters, latitude) => meters / 0.075 / Math.cos(latitude * Math.PI / 180);

            map.on('idle', () => {
                // $("#btnLocation").prop('disabled', false);
            })
    
            map.addControl(geolocate);
            map.addControl(new mapboxgl.NavigationControl());
        }

        const getPlaceTimezone = async (lng, lat) => {
            let result;
            let _token = "<?= csrf_token() ?>";
            try {
                try {
                    result = await $.ajax({
                        type: 'POST',
                        url: "<?= url('/time_attendance/getPlaceTimezoneMapbox') ?>",
                        dataType: 'json',
                        headers: {'X-CSRF-TOKEN': _token},
                        data: {
                            lng: lng,
                            lat: lat,
                            zone_workdays: convertZone(zone)
                        },
                        success: function (resp) {
                            if(resp.result == false){
                                alert('Location can\'t read by server, please try again');
                                location.reload();
                            } 
                            map_place = resp.place;
                            map_timezone = resp.timezone;
                        },
                        error: function (jqXHR, exception) {
                            map_timezone = map_timezone
                            map_place = '';
                        },
                    });
                    return result;
                } catch (error) {
                    if(error.status == 419){
                        swal({
                            title: 'Please refresh page',
                        }).then(function(){ 
                            location.reload();
                        });
                    }
                }
            } catch (error) {
                getPlaceTimezone(lng, lat);
            }
        }

        function convertTimezone(time, zone) {
            let zone_ = zone.toLowerCase();
            let time_ = parseInt(time);
            let data = {
                'asia/jakarta' : time_,
                'asia/makassar' : time_ + 1,
                'asia/jayapura' : time_ + 2,
                'wib' : time_,
                'wita' : time_ + 1,
                'wit' : time_ + 2,
            }
            return data[zone_];
        }

        function convertZone(zone) {
            let zone_ = zone.toLowerCase();
            let data = {
                'asia/jakarta' : 'WIB',
                'asia/makassar' : 'WITA',
                'asia/jayapura' : 'WIT',
                'wib' : 'Asia/Jakarta',
                'wita' : 'Asia/Makassar',
                'wit' : 'Asia/Jayapura',
            }
            return data[zone_];
        }

        function refreshMap() {
            $("#btnLocation_loading").hide();
            $("#btnLocation").prop('disabled', true);
            drawMap()
        }

        const getSnapshoot = async () => {
            try {
                await Webcam.snap(function (data_uri) {
                    img = data_uri;
                });
                if(img==''){
                    getSnapshoot();
                }
                return img;
            } catch (error) {
                getSnapshoot();
            }
        }

        const getServerTime = async (js=false) => {
            let result;
            let result_offline = {
                'Y':'<?= date('Y') ?>',
                'M':'<?= date('m') ?>',
                'D':'<?= date('d') ?>',
                'hour':'<?= date('H') ?>', 
                'minute':'<?= date('i') ?>'
            };
            let dt = new Date();
            let result_offline_js = {
                'Y':dt.getFullYear(), 
                'M':dt.getMonth() + 1, 
                'D':dt.getDate()
            };
            try {
                try {
                    result = await $.ajax({
                        type: 'GET',
                        url: "<?= url('/time_attendance/getServerTime') ?>",
                        dataType: 'json',
                        success: function (resp) {
                            return resp
                        },
                        error:function(err){
                            if(js != true){
                                return result_offline
                            } else {
                                return result_offline_js
                            }
                        },
                    });
                } catch (err){
                    if(js != true){
                        return result_offline
                    } else {
                        return result_offline_js
                    }
                }
                return result;
            } catch (error) {
                getServerTime()
            }
        }

        $("#btnRecord").click(function () {
            if(accessThisMenu.can_create == false){
                //variabel accessThisMenu ada di vendor/adminlte/master.blade
                return false;
            }
            getServerTime(true).then(server => {
                let sch_time_in  = "<?= @$attendance[0]->schedule_time_in ?>";
                let current_dates_now  = "<?= @$attendance[0]->current_dates ?>";
                let s_m = (server.M.toString().length < 2 && server.M < 10) ? `0${server.M}` : server.M;
                let s_d = (server.D.toString().length < 2 && server.D < 10) ? `0${server.D}` : server.D;
                let server_today = `${server.Y}-${s_m}-${s_d}`;
                let date_of_today = moment(server_today, 'YYYY-MM-DD').valueOf();
                if(sch_time_in == '' || date_of_today > moment(current_dates_now, 'YYYY-MM-DD').valueOf()){
                    //jika tanggal current_date lebih kecil dr tanggal server hari ini maka reload
                    let redirect = "<?= url('/') ?>";
                    window.location.reload();
                } else {
                    server_hour_now     = server.hour;
                    server_minute_now   = server.minute;

                    let actual_time_out = "<?= @$attendance[0]->actual_time_out ?>";
                    let shift_code_before = "<?= @$attendance[1]->shift_code; ?>";
                    let actual_time_out_b = "<?= @$attendance[1]->actual_time_out ?>";
                    let schedule_time_out = "<?= @$attendance[1]->schedule_time_out ?>";
                    let server_hour     = convertTimezone(server_hour_now, zone);
                    let server_minute   = server_minute_now;
                    let hours           = (server_hour < 10) ? "0" : "";
                    let today           = "<?= date('Y-m-d') ?>" + ' '+ hours + server_hour+ ':' + server_minute;
                    let str_day_now_    = moment(today, 'YYYY-MM-DD HH:mm').valueOf();
                    let str_schedule_out= moment(schedule_time_out, 'YYYY-MM-DD HH:mm').add(4, 'hours').valueOf();

                    if(shift_code_before == 'SHIFT_3' && actual_time_out_b != '' && str_day_now_ <= str_schedule_out){
                        //kondisi jika setelah trdapat cekout shift 3 lalu absen lagi (max. 1 jam dr schedule time_out)
                        $('#dialogConfirmUpdate').modal('show');
                    } else {
                        if (actual_time_out == null || actual_time_out == "" || actual_time_out == " ") {
                            step_now = 0;
                            nextStep(step_now);
                        } else {
                            $('#dialogConfirmUpdate').modal('show');
                        }
                    }
                }
            });
        });

        $("#btnCapture").click(function () {
            getSnapshoot().then(res => {
                $("#avatar").attr('src', res);
                Webcam.reset();
                nextStep(step_now);
            });
        });

        $(".close_camera").click(function () {
            Webcam.reset();
        });

        $("#btnLocation").click(function () {
            $("#btnLocation").hide();
            $("#btnLocation_loading").show();

            checkConnection().then(res => {
                if(res=='on'){
                    getPlaceTimezone(lng, lat).then(res => {
                        zone = res.timezone.properties.TZID;
                        nextStep(step_now);
                    })
                } else {
                    nextStep(step_now);
                }
            });
        });

        $("#btnCancel").click(function () {
            $('#dialogConfirmUpdate').modal('hide');
        });

        function checkoutAttendance(zone) {
            $("#btnSaveAttendance").attr('disabled', true);
            getServerTime().then(server => {
                $("#btnSaveAttendance").removeAttr('disabled');
                server_hour_now     = server.hour;
                server_minute_now   = server.minute;
                
                $("#shift_").html('');
                let allow_next_days = "<?= @$attendance[0]->allow_checkout_nextdays ?>";
                let actual_time_in  = "<?= @$attendance[0]->actual_time_in ?>";
                let actual_time_out = "<?= @$attendance[0]->actual_time_out ?>";
                let schedule_time_in  = "<?= @$attendance[0]->schedule_time_in ?>";
                let schedule_start  = "<?= @$attendance[0]->start_time ?>" != '' ? "<?= @$attendance[0]->start_time ?>" : "00:00:00";
                let shift_code      = "<?= @$attendance[0]->shift_code; ?>";
                let holiday_today     = "<?= @$attendance[0]->id_holiday; ?>";
                let request_today     = "<?= @$attendance[0]->id_request; ?>";
                let holiday_yesterday = "<?= @$attendance[1]->id_holiday; ?>";
                let request_yesterday = "<?= @$attendance[1]->id_request; ?>";
                let shift_code_before = "<?= @$attendance[1]->shift_code; ?>";
                let allow_next_days_b = "<?= @$attendance[1]->allow_checkout_nextdays ?>";
                let schedule_time_in_b= "<?= @$attendance[1]->schedule_time_in ?>";
                let schedule_time_out = "<?= @$attendance[1]->schedule_time_out ?>";
                let actual_time_in_b = "<?= @$attendance[1]->actual_time_in ?>";
                let actual_time_out_b = "<?= @$attendance[1]->actual_time_out ?>";
                let schedule_start_b  = "<?= @$attendance[1]->start_time ?>" != '' ? "<?= @$attendance[1]->start_time ?>" : "00:00:00";
                let server_hour     = convertTimezone(server_hour_now, zone);
                let server_minute   = server_minute_now;
                let hours           = (server_hour < 10) ? "0" : "";

                attendance_time     = hours + server_hour+ ':' + server_minute;

                let str_day_now     = moment("<?= date('Y-m-d') ?>" +' '+attendance_time, 'YYYY-MM-DD HH:mm').valueOf();
                let str_now         = moment(attendance_time, 'HH:mm').valueOf();
                let str_schedule_in = moment(schedule_start, 'HH:mm').valueOf();
                let str_4h          = moment(schedule_start, 'HH:mm:ss').add(4, 'hours').valueOf();
                let str_12          = moment('12:00', 'HH:mm').valueOf();
                let str_4h_a        = moment(schedule_time_in, 'YYYY-MM-DD HH:mm:ss').add(4, 'hours').valueOf();
                let str_4h_b        = moment(schedule_time_in_b, 'YYYY-MM-DD HH:mm:ss').add(4, 'hours').valueOf();
                let str_schedule_out = moment(schedule_time_out, 'YYYY-MM-DD HH:mm').add(1, 'hours').valueOf();
                let str_schedule_out_b = moment(schedule_time_out, 'YYYY-MM-DD HH:mm:ss').valueOf();
                let str_4h_schedule_out_b = moment(schedule_time_out, 'YYYY-MM-DD HH:mm:ss').add(4, 'hours').valueOf();

                if(shift_code == 'Regular' || shift_code == 'Half Regular' || shift_code == 'SHIFT_1' || shift_code == 'SHIFT_2'){
                    if(shift_code_before == 'SHIFT_3'){
                        if(actual_time_out_b != '' && str_day_now <= str_schedule_out){
                            console.log('a');
                            // kondisi sudah absen cekout utk shift 3, lalu melakukan cekout lg
                            shift_name      = "End";
                        } else if(str_day_now >= str_4h_b && str_day_now <= str_schedule_out ){ 
                            // kondisi absen cekout utk shift 3, dan kondisi sudah cekout SHift3 lalu cekout lagi (max. 1 jam dr schedule time_out)
                            console.log('b');
                            shift_name      = "End";
                        } else if(str_day_now < str_4h_a || str_day_now < str_4h_b){
                            // kondisi absen pertama utk shift 3 jika lupa blm cekin di jam awal
                            if(str_day_now >= str_schedule_out_b && ((actual_time_in_b!='' && actual_time_out_b=='') || (actual_time_in_b=='' && actual_time_out_b=='' && str_day_now > str_4h_a)) ){
                                console.log('c');
                                shift_name  = "End";
                            } else {
                                console.log('d');
                                shift_name  = "Start";
                            }
                        } 
                    } else {
                        if(shift_code_before == 'FLEX_SHIFT'){
                            if(allow_next_days_b== '1' && actual_time_out_b == ''){
                                console.log('e');
                                shift_name  = "End";
                            } else {
                                if(actual_time_in != '' && str_now >= str_schedule_in){
                                    console.log('f');
                                    shift_name  = "End";
                                } else {
                                    console.log('f1');
                                    shift_name  = "Start";
                                }
                            }
                        } else {
                            if(str_now < str_4h){
                                if(str_now > str_schedule_in && actual_time_in != ''){
                                    console.log('g');
                                    shift_name  = "End";
                                } else {
                                    console.log('h');
                                    shift_name  = "Start";
                                }
                            } else {
                                console.log('i');
                                shift_name      = "End";
                            }
                        }
                    }
                } else {
                    if(shift_code_before == 'SHIFT_3' || shift_code == 'SHIFT_3' ){
                        if(shift_code == 'OFF'){
                            if(str_day_now >= str_schedule_out_b && ((actual_time_in_b!='' && actual_time_out_b=='') || (actual_time_in_b=='' && actual_time_out_b=='' && str_day_now > str_4h_a)) ){
                                console.log('j');
                                shift_name  = "End";
                            } else {
                                if(str_day_now >= str_4h_schedule_out_b && actual_time_in == ''){
                                    console.log('k');
                                    shift_name  = "Start";
                                } else {
                                    console.log('l');
                                    shift_name  = "End";
                                }
                            } 
                        } else {
                            if(actual_time_out_b != '' && str_day_now <= str_schedule_out){
                                // kondisi sudah absen cekout utk shift 3, lalu melakukan cekout lg
                                console.log('m');
                                shift_name      = "End";
                            } else if(shift_code_before == 'SHIFT_3' && str_day_now >= str_4h_b && str_day_now <= str_schedule_out ){ 
                                // kondisi absen cekout utk shift 3, dan kondisi sudah cekout SHift3 lalu cekout lagi (max. 1 jam dr schedule time_out)
                                console.log('n');
                                shift_name      = "End";
                            } else if(str_day_now < str_4h_a || str_day_now < str_4h_b){
                                // kondisi absen cekin utk shift 3
                                if(str_day_now >= str_schedule_out_b && ((actual_time_in_b!='' && actual_time_out_b=='') || (actual_time_in_b=='' && actual_time_out_b=='' && str_day_now > str_4h_a)) ){
                                    console.log('o');
                                    shift_name  = "End";
                                } else {
                                    console.log('p');
                                    shift_name  = "Start";
                                } 
                            }
                        }
                    } else { // masuk kondisi FLEX_SHIFT ATAU OFF
                        if(shift_code_before == 'FLEX_SHIFT'){
                            if(actual_time_in_b == '' || actual_time_out_b == ''){ // jika hari kemarin tidak ada absen sama sekali
                                if(actual_time_in != '' && str_now >= str_schedule_in){
                                    console.log('q1');
                                    shift_name  = "End";
                                } else {
                                    console.log('q2');
                                    shift_name  = "Start";
                                }
                            } else {
                                if(allow_next_days_b== '1' && actual_time_out_b == ''){
                                    console.log('q3');
                                    shift_name  = "End";
                                } else {
                                    if(actual_time_in != '' && str_now >= str_schedule_in){
                                        console.log('r');
                                        shift_name  = "End";
                                    } else {
                                        console.log('s');
                                        shift_name  = "Start";
                                    }
                                }
                            }
                        } else {
                            if(actual_time_in != ''){
                                console.log('t');
                                shift_name      = "End";
                            } else {
                                console.log('u');
                                shift_name      = "Start";
                            }
                        }
                    }
                }
                
                $("#shift_name").html(shift_name + ' Time : ');
                $("#shift_").html(shift_name);
                $("#dialogConfirmSave_date").html(attendance_time);

            });
        }

        function closeConfirmModal() {
            $("#dialogConfirmSave").toggle();
        }

        function submitAttendance() {
            let _token      = "<?= csrf_token() ?>";
            var datenow     = "<?= date('Y-m-d') ?>";
            var timenow     = parseInt("<?= date('H') ?>");
            var shift       = $("#shift_").html().toLowerCase();
            var valueabsen  = {};
            var dataabsen   = new Array;
            
            // if(timenow < 12){
            if(shift == 'start'){
                valueabsen["start_time"] = datenow.concat(" ", attendance_time);
                valueabsen["end_time"] = "";
            // } else if(timenow >= 12) {
            } else {
                valueabsen["start_time"] = "";
                valueabsen["end_time"] = datenow.concat(" ", attendance_time);
            }
            valueabsen["id_user"]           = "<?= Session::get('id_user') ?>";
            valueabsen["id_workdays"]       = "<?= @$attendance[0]->id_workdays ?>",
            valueabsen["date"]              = datenow;
            valueabsen["current_latitude"]  = lat,
            valueabsen["current_longitude"] = lng,
            valueabsen["image_attachment"]  = img,
            valueabsen["shift"]             = shift,
            valueabsen["_token"]            = _token,
            valueabsen["mapboxAccessToken"] = mapboxAccessToken,
            valueabsen["map_place"]         = map_place,
            valueabsen["map_timezone"]      = map_timezone,
            
            dataabsen.push(valueabsen);
            localStorage.setItem("dataabsen",JSON.stringify(dataabsen));
            
            var formdata = new FormData();
            var id_workdays = "";

            $.ajax({
                type: 'POST',
                url: "<?= url('/time_attendance/attendance/update?id=' . @$attendance[0]->id_workdays) ?>",
                headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                dataType: 'json',
                data: {
                    id_workdays: id_workdays,
                    current_latitude: lat,
                    current_longitude: lng,
                    image_attachment: img,
                    shift: shift,
                    _token: _token,
                    mapboxAccessToken: mapboxAccessToken,
                    starttimeabsen: valueabsen["start_time"],
                    endtimeabsen: valueabsen["end_time"],
                    map_place : map_place,
                    map_timezone : map_timezone
                },
                success: function (result) {
                    localStorage.removeItem("dataabsen");
                    $("#btnSaveAttendance").show();
                    $("#btnSaveAttendance_loading").hide();
                    $("#btnSaveAttendance").attr('disabled', true);
                    swal({
                        icon: 'success',
                        title: result['message'],
                        text: ' ',
                        dangerMode: false,
                    }).then(function(){ 
                        location.reload();
                    });
                    // alert(result['message']);
                },
                error: function (result) {
                    $("#btnSaveAttendance").show();
                    $("#btnSaveAttendance_loading").hide();
                    $("#btnSaveAttendance").attr('disabled', true);
                    swal({
                        icon: 'success',
                        title: 'Your connection is unstable, attendance record has been recorded locally',
                        text: ' ',
                        dangerMode: true,
                    }).then(function(){ 
                        location.reload();
                    });
                    // alert("Your connection is unstable, attendance record has been recorded locally");
                },
            });
        }

        $("#btnSaveAttendance").click(function () {
            $("#btnSaveAttendance").hide();
            $("#btnSaveAttendance_loading").show();
            submitAttendance()
        });

        $(window).on('offline', function(){ // kondisi saat koneksi internet berubah dari on ke off
            swal({
                icon: 'error',
                title: 'You are offline, connection is unstable',
                text: ' ',
                dangerMode: false,
            });
        });

        $(window).on('online', function(){ // kondisi saat koneksi internet berubah dari off ke on
            swal({
                icon: 'success',
                title: "You are online",
                text: ' ',
                dangerMode: false,
            });
        });

    </script>

<?php 
	if($employee){
		$idCompany = $employee->id_company;
	}
	else{
		$idCompany = null;
	}
    $_show = false;
    $idShow = [];
    $_surveyActive = \DB::table('hr_survey_header as hsh')
        ->leftJoin('relation_department_surveys as rds', 'rds.id_survey_header', '=', 'hsh.id_survey_header')
        ->leftJoin('relation_regional_surveys as rgs', 'rgs.id_survey_header', '=', 'hsh.id_survey_header')
        ->leftJoin('relation_branch_surveys as rbs', 'rbs.id_survey_header', '=', 'hsh.id_survey_header')
        ->leftJoin('relation_jobgrade_surveys as rjs', 'rjs.id_survey_header', '=', 'hsh.id_survey_header')
        ->leftJoin('relation_principal_surveys as rps', 'rps.id_survey_header', '=', 'hsh.id_survey_header')
        ->leftJoin('hr_survey_history as hshi', 'hshi.id_survey_header', '=', 'hsh.id_survey_header')
        ->select('hsh.id_survey_header', 'hshi.end_date', 'hshi.id_survey_history', 'rds.id_dept as id_department', 'rgs.id_region', 'rbs.id_branch', 'rjs.id_job_grade', 'rps.id_principal', 'hsh.is_cross_company_os', 'hsh.id_company')
        ->where(function ($query){
            $query->whereDate('hshi.start_date', '<=', date('Y-m-d'))->whereDate('hshi.end_date', '>=', date('Y-m-d'));
            $query->where('hshi.status', 'A');
        })
        ->where('hsh.published', true)->where('hsh.status', 'A')
        ->whereRaw('(
			(hsh.is_cross_company_os = FALSE AND "hsh"."id_company" = ?)
			OR
			(hsh.is_cross_company_os = TRUE)
		)', [$idCompany])
        ->where('hsh.survey_category', 'Survey')
        ->orderBy('hshi.end_date')->get();
    if(@$_surveyActive){
        $id_survey_header = [];     $activeSurvey = [];
        $_myEmployee = \DB::table('hr_employee as he')->select('he.id_employee','mjp.id_dept','mb.id_region','mpd.id_branch', 'mpr.id_job_grade', 'rpp.id_principal')
                ->leftJoin('master_position_detail as mpd', 'mpd.id_employee', '=', 'he.id_employee')
                ->leftJoin('master_branch as mb', 'mb.id_branch', '=', 'mpd.id_branch')
                ->leftJoin('master_position_routing as mpr', 'mpr.id_routing', '=', 'mpd.id_position_routing')
                ->leftJoin('master_job_position as mjp', 'mjp.id_position', '=', 'mpr.id_position')
                ->leftJoin('master_job_grade as mjg', 'mjg.id_job_grade', '=', 'mpr.id_job_grade')
                ->leftJoin('relation_positiondetail_principal as rpp', 'rpp.id_position_detail', 'mpd.id_position_detail')
                ->where('he.id_user', session('id_user'))
                ->where('he.status', 'A')
                ->where('he.id_company', session('id_company'))
                ->first();

        $allSurvey = [];
        $surveyDetail = [];
        $idSurveyHistory = [];
        foreach ($_surveyActive as $key => $val) {
            $idSurveyHistory[] = $val->id_survey_history;
            $idSurvey = $val->id_survey_header;
            if(!in_array($idSurvey, $allSurvey)){
                $allSurvey[] = $idSurvey;
                $surveyDetail[$idSurvey] = [
                    'id_department' => [],
                    'id_region' => [],
                    'id_branch' => [],
                    'id_job_grade' => [],
                    'id_principal' => [],
                    'is_cross_company_os' => $val->is_cross_company_os,
                ];
                $activeSurvey[] = ['id'=>$idSurvey, 'end'=>$val->end_date];
            }
            $id_department = $val->id_department;
            $id_region = $val->id_region;
            $id_branch = $val->id_branch;
            $id_job_grade = $val->id_job_grade;
            $id_principal = $val->id_principal;

            if(!is_null($id_department) && !in_array($id_department, $surveyDetail[$idSurvey]['id_department'])){
                $surveyDetail[$idSurvey]['id_department'][] = $id_department;
            }
            if(!is_null($id_region) && !in_array($id_region, $surveyDetail[$idSurvey]['id_region'])){
                $surveyDetail[$idSurvey]['id_region'][] = $id_region;
            }
            if(!is_null($id_branch) && !in_array($id_branch, $surveyDetail[$idSurvey]['id_branch'])){
                $surveyDetail[$idSurvey]['id_branch'][] = $id_branch;
            }
            if(!is_null($id_job_grade) && !in_array($id_job_grade, $surveyDetail[$idSurvey]['id_job_grade'])){
                $surveyDetail[$idSurvey]['id_job_grade'][] = $id_job_grade;
            }
            if(!is_null($id_principal) && !in_array($id_principal, $surveyDetail[$idSurvey]['id_principal'])){
                $surveyDetail[$idSurvey]['id_principal'][] = $id_principal;
            }
        }

        foreach ($surveyDetail as $key => $val) {
            $surveyFalse = [];
            if(count($val['id_department']) < 1 && count($val['id_region']) < 1 && count($val['id_branch']) < 1 && count($val['id_job_grade']) < 1 && count($val['id_principal']) < 1){
                $id_survey_header[] = $key;
            } else {
                if(count($val['id_department']) > 0 && !in_array(@$_myEmployee->id_dept, $val['id_department'])){
                    $surveyFalse[] = false;
                }
                if(count($val['id_region']) > 0 && !in_array(@$_myEmployee->id_region, $val['id_region'])){
                    $surveyFalse[] = false;
                }
                if(count($val['id_branch']) > 0 && !in_array(@$_myEmployee->id_branch, $val['id_branch'])){
                    $surveyFalse[] = false;
                }
                if(count($val['id_job_grade']) > 0 && !in_array(@$_myEmployee->id_job_grade, $val['id_job_grade'])){
                    $surveyFalse[] = false;
                }
                if(count($val['id_principal']) > 0 && !in_array(@$_myEmployee->id_principal, $val['id_principal'])){
                    $surveyFalse[] = false;
                }
            }
            if(count($surveyFalse) < 1){
                $id_survey_header[] = $key;
            }
        }

        if(count($id_survey_header) > 0){
            $id_survey_header = collect($id_survey_header)->unique()->toArray();

            $_surveyAnswer = \DB::table('hr_survey_answer_user_header')
                    ->select('id_survey_header')
                    ->whereIn('id_survey_header', $id_survey_header)
                    ->whereIn('id_survey_history', $idSurveyHistory)
                    ->where('id_company', session('id_company'))
                    ->where('id_employee', @$_myEmployee->id_employee)
                    ->get()->pluck('id_survey_header')->all();

            if(count($_surveyAnswer) < count($id_survey_header)){
                $idShow = collect($id_survey_header)->diff($_surveyAnswer)->values();
            }
        }
    }
?>

@if(count($idShow)>0)
@include('employee.employee_survey.employee_answer.survey')
@endif

<script type="text/javascript">
    id_show = <?= json_encode($idShow); ?>;//
    let activeSurvey = <?= json_encode($activeSurvey); ?>;
    let getSurveyParameterUrl = "{{ Request::get('s') }}";
    let surveyToday = moment('<?= date('Y-m-d'); ?>', 'YYYY-MM-DD').valueOf();

    if(id_show.length > 0){
        let dateForAnnouncement = localStorage.getItem("now_date");
        let announc_old = localStorage.getItem("announ_old");
        let announ_count = localStorage.getItem("announ_count");

        let todayForAnnouncement = '<?= date('Y-m-d') ?>';
        $(activeSurvey).each(function (i, val) {
            if(!("{{ Request::get('s') }}" != '' && id_show.length > 0)){
                //pengondisian utk survey yg selain dari mobile agar bs menampilkan announcement dulu (jika barengan)
                if(announc_old == announ_count && dateForAnnouncement == todayForAnnouncement){
                    //mendahulukan showing announcement dulu jika ada bersamaan dengan modal survey (kecuali survey yg berasal klik dari mobile)
                    if(getSurveyParameterUrl != ''){
                        if($.inArray(parseInt(getSurveyParameterUrl), id_show) !== -1 ) {
                            if(val.id == getSurveyParameterUrl){
                                surveyEnd = moment(val.end, 'YYYY-MM-DD').valueOf();
                                if(surveyToday >= surveyEnd){
                                    //jika hari terakhir survey namun masih blm dijawab maka tombol skip dihidden
                                    $("#skip_survey").hide();
                                }
                                show(getSurveyParameterUrl);
                            }
                        } else {
                            if(val.id == id_show[0]){
                                surveyEnd = moment(val.end, 'YYYY-MM-DD').valueOf();
                                if(surveyToday >= surveyEnd){
                                    $("#skip_survey").hide();
                                }
                                show(id_show[0]);
                            }
                        }
                    } else {
                        if(val.id == id_show[0]){
                            surveyEnd = moment(val.end, 'YYYY-MM-DD').valueOf();
                            if(surveyToday >= surveyEnd){
                                $("#skip_survey").hide();
                            }
                            show(id_show[0]);
                        }
                    }
                }
            } else {
                //pengondisian utk survey yg berasal dari mobile agar tidak menampilkan announcement dulu (jika barengan)
                if($.inArray(parseInt(getSurveyParameterUrl), id_show) !== -1 ) {
                    if(val.id == getSurveyParameterUrl){
                        surveyEnd = moment(val.end, 'YYYY-MM-DD').valueOf();
                        if(surveyToday >= surveyEnd){
                            //jika hari terakhir survey namun masih blm dijawab maka tombol skip dihidden
                            $("#skip_survey").hide();
                        }
                        show(getSurveyParameterUrl);
                    }
                }
            }
        });
    }

</script>