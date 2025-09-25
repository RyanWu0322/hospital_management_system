$(document).ready(function() {
    // 更新護理師與病人的比例顏色
    function updateNursePatientRatio() {
        $('.nurse-patient-ratio').each(function() {
            var ratio = parseFloat($(this).text());
            if (ratio < 3) {
                $(this).css('color', 'green');
            } else if (ratio > 5) {
                $(this).css('color', 'yellow');
            } else if (ratio > 7) {
                $(this).css('color', 'red');
            }
        });
    }

    // 偵聽 Dashboard 的加載事件並更新比率顏色
    updateNursePatientRatio();

    // 假設有一個更新病床數量的 AJAX 請求
    $('#update-beds-form').submit(function(event) {
        event.preventDefault();
        var totalBeds = $('#total_beds').val();

        $.ajax({
            url: 'manage_beds.php',
            type: 'POST',
            data: { total_beds: totalBeds },
            success: function(response) {
                alert('病床數量已更新');
                location.reload(); // 刷新頁面以更新床位數量
            }
        });
    });

    // Counter 插件的簡單實現，用於病例數量統計
    $('.counter').each(function() {
        var $this = $(this),
            countTo = $this.attr('data-count');
        $({ countNum: $this.text() }).animate({
            countNum: countTo
        },
        {
            duration: 2000,
            easing: 'swing',
            step: function() {
                $this.text(Math.floor(this.countNum));
            },
            complete: function() {
                $this.text(this.countNum);
            }
        });
    });

    // 更新儀表板資料
    function updateDashboard() {
        $.ajax({
            url: 'dashboard_data.php',
            type: 'GET',
            success: function(response) {
                var data = JSON.parse(response);
                $('#doctors-count').text(data.doctors);
                $('#nurses-count').text(data.nurses);
                $('#patients-count').text(data.patients);
                $('#total-beds').text(data.total_beds);
                $('#occupied-beds').text(data.occupied_beds);
                $('#vacant-beds').text(data.vacant_beds);
                $('#medical-records').text(data.medical_records);
                $('#nurse-patient-ratio').text(data.nurse_patient_ratio);
                updateNursePatientRatio();
            }
        });
    }

    // 每10秒更新一次數據
    setInterval(updateDashboard, 10000);
    updateDashboard(); // 初始化更新一次
});
