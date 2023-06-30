<!DOCTYPE HTML>
<html>
    <head>
    <meta http-equiv="refresh" content="5" />
    <link rel="stylesheet" href="style.css">
    </head>

    <body>

        <br><br><br>
        <!-- menampilkan grafik dengan id chartContainer -->
        <!-- ukuran grafik: tinggi 550 pixel, dan maksimal lebar 920 pixel-->
        <div id="chartContainer" style="height: 250px; max-width: 520px; margin: 0px auto; "></div>

        <h2 style="text-align: center">Monitoring Suhu Real Time</h2>
        <div class="card-normal" id="normal">
        <div class="container"> 
            <p>Suhu Masih di batas Aman</p>
            <p> Suhu Sekarang : <span id="tampilsuhu" style="color : blue;"></span> Celcius</p>
        </div>
        </div>

        <div class="card-danger" id="danger">
        <div class="container">
            <h3><span>PERINGATAN !!!</span></h3>
            <p>Suhu Melewati Batas Normal</p> 
            <p> Suhu Sekarang : <span id="tampilsuhu1"></span> Celcius</p>
        </div>
        </div>

        
        

        <!-- import library canvasjs dan jquery dengan cdn -->
        <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>

        <script>


            window.onload = function () {

                //inisialisasi data array dps kepanjangan dari data points
                var dps = [];
                var dataLength = 20; // panjang data yang ditampilkan horizontal, ditampilkan di bagian bawah
                var updateInterval = 1000; // setiap 1 detik data di refresh
                var xVal = 0;
                var yVal = 0;

                //inisialisasi chart js
                var chart = new CanvasJS.Chart("chartContainer", {
                    title: {
                        text: "Grafik Suhu Realtime" // memberi judul grafik
                    },
                    data: [{
                        type: "line", //tipe grafik yang digunakan, lihat di situsnya untuk lihat gaya yang lain
                        dataPoints: dps //dps adalah data yang digunakan
                    }]
                });

                var updateChart = function (count) {
                    //data json diambil dari alamat /getdata
                    $.getJSON("http://localhost/projectakhirIOT/getdata.php", function (data) {

                        var suhu = data.suhu //mengambil data spesifik rate float
                //alert(suhu):
                        var audio = new Audio('alarm.mp3');
                        var danger = document.getElementById('danger');
                        var normal = document.getElementById('normal');
                        document.getElementById("tampilsuhu").innerHTML = suhu;
                        document.getElementById("tampilsuhu1").innerHTML = suhu;
                        console.log(suhu) // menampilkan data dengan console.log hanya terlihat saat mode inspection
                        if (suhu > 30) {
                            danger.style.visibility='visible';
                            normal.style.visibility='hidden';
                            audio.play();
                        }else{
                            danger.style.visibility='hidden';
                            normal.style.visibility='visible';
                        }
                        yVal = suhu //mengisi variabel yVal dengan data usd

                        count = count || l;

                        //melakukan perulangan data dengan for agar data dapat dijalankan
                        for (var j = 0; j < count; j++) {
                            dps.push({
                                x: xVal,
                                y: yVal
                            });
                            xVal++;
                        }

                        //jika datapoints telah melewati datalength
                        if (dps.length > dataLength) {
                            dps.shift(); // maka hapus data awal dengan fungsi shift()
                        }

                    })
                    chart.render();
                }; 
                
                //jalankan fungsi updateChart diatas
                updateChart(dataLength);

                //fungsi agar data dapat diupdate setiap 1000 detik sekali
                setInterval(function () {
                    updateChart()
                }, updateInterval);
                
  
            }

        </script>

    </body>
</html>