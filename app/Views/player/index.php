<?= $this->extend('layout'); ?>

<?= $this->section('content'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header"></section>

  <!-- Main content -->
  <section class="content">
      <div class="container-fluid">
        <div class="row">
            <div class="col-3">
                <label for="channel_name">Search By Channel</label>
                <select id="channel_name" style="width: 100%;">
                    <option></option>
                    <option value="0">Channel 0</option>
                    <option value="1">Channel 1</option>
                    <option value="2">Channel 2</option>
                    <option value="3">Channel 3</option>
                    <option value="4">Channel 4</option>
                    <option value="5">Channel 5</option>
                    <option value="6">Channel 6</option>
                    <option value="7">Channel 7</option>
                    <option value="8">Channel 8</option>
                    <option value="9">Channel 9</option>
                    <option value="10">Channel 10</option>
                    <option value="11">Channel 11</option>
                    <option value="12">Channel 12</option>
                    <option value="13">Channel 13</option>
                    <option value="14">Channel 14</option>
                    <option value="15">Channel 15</option>
                    <option value="16">Channel 16</option>
                </select>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label for="time_start">Start Time</label>
                    <input type="text" class="form-control daterangepicker-in-form" id="time_start" name="time_start" placeholder="Select a date and time">
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label for="time_end">End Time</label>
                    <input type="text" class="form-control daterangepicker-in-form" id="time_end" name="time_end" placeholder="Select a date and time">
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-2">
                <button id="btn_reset" class="btn btn-primary">Reset</button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table id='table_files' class='table table-bordered table-hover'>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Audio File</th>
                            <th>Description</th>
                            <th>Download</th>
                            <th> <input type='checkbox'> </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
  </section>
</div>

<?= $this->endSection(); ?>


<?= $this->section('extra_js'); ?>
<script>
     $(document).ready(function() {
        $('#channel_name').select2({
            placeholder: 'Select channel',
            allowClear: true
        });
        $('#channel_name').on('select2:select', function (e) {
            tableFiles.draw();
        });
        $('#channel_name').on('select2:clear', function (e) {
            tableFiles.draw();
        });

        $('#time_start').daterangepicker({
            singleDatePicker: true,
            timePicker: true,
            timePicker24Hour: true,
            timePickerSeconds: true,
            showDropdowns: true, 
            autoApply: true, 
            locale: {
                format: 'YYYY-MM-DD HH:mm:ss'
            },
            startDate:  moment('2024-08-01 00:00:00', 'YYYY-MM-DD HH:mm:ss')
        });
        $('#time_start').on('apply.daterangepicker', function(ev, picker) {
            tableFiles.draw();
        });
        $('#time_end').daterangepicker({
            singleDatePicker: true,
            timePicker: true,
            timePicker24Hour: true,
            timePickerSeconds: true,
            showDropdowns: true, 
            autoApply: true, 
            locale: {
                format: 'YYYY-MM-DD HH:mm:ss'
            },
            startDate:  moment().format('YYYY-MM-DD HH:mm:ss')
        });
        $('#time_end').on('apply.daterangepicker', function(ev, picker) {
            tableFiles.draw();
        });

        // let debounceTimeout;
        // const delay = 500; 
        // $('#file_name').on('input', function() {
        //     clearTimeout(debounceTimeout);
        //     debounceTimeout = setTimeout(function() {
        //         tableFiles.draw();                                
        //     }, delay);
        // });

        let tableFiles = $('#table_files').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
                url: "<?= base_url('player/getall') ?>", 
                type: "POST",
                data: function (d) {
                    let channel = $('#channel_name').select2('data');
                    if(channel.length > 0){
                        d.channel = channel[0]['id']; 
                    }
                    // d.file_name = $('#file_name').val();
                    d.time_start = $('#time_start').val();
                    d.time_end = $('#time_end').val();
                }                
            },
            drawCallback: function (settings) {
                let audioElements = document.querySelectorAll('audio');
                audioElements.forEach(audio => {
                    audio.addEventListener('play', () => {
                        audioElements.forEach(otherAudio => {
                            if (otherAudio !== audio) {
                                otherAudio.pause();
                            }
                        });
                    });
                });
            }
        });

        $('#btn_reset').on('click', function() {
            let now = moment();
            // $("#file_name").val('');
            $('#channel_name').val(null).trigger('change');
            $('#time_start').data('daterangepicker').setStartDate('2024-08-01 00:00:00')            
            $('#time_end').data('daterangepicker').setStartDate(now)            
            tableFiles.draw();
        });
               
    });   
</script>
<?= $this->endSection(); ?>