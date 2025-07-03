<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Kata Pengantar - Laporan Survei </title>
    <style>
        @page {
            margin-top: 2cm;
            margin-bottom: 2cm;
            margin-left: 2cm;
            margin-right: 2cm;
        }

        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100vh;
        }

        .container img {
            width: 150px;
            margin: 6rem 0;
        }

        .title {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            text-transform: uppercase;
        }

        .subtitle {
            font-size: 24px;
            font-weight: 700;
            color: #333;
        }

        .subtitle-wrapper {
            margin-top: 120px;
        }

        img {
            width: 6rem;
            margin-top: 120px
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 120px;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 8px;
        }

        .table th {
            text-align: center;
            vertical-align: middle;
        }

        .table td {
            text-align: left;
            vertical-align: top;
        }

        .paragraf {
            text-align: justify;
            text-indent: 25px;
            line-height: 2em;
        }


        .ttd {
            width: 100%;
        }

        .ttd td {
            width: 33.33%;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container" style="display: flex; justify-content: space-between; height: 100%;">
        <div class="title-wrapper" style="margin-bottom: 120px">
            <div class="title" style="font-size: 14">Lembar Pengesahan Laporan</div>
            <div class="title" style="font-size: 14">{{$rtm->name}}</div>
            <div class="title" style="font-size: 14">TAHUN AKADEMIK {{ $reportData['tahun_akademik'] ?? '2022/2023' }}</div>
            <div class="title" style="font-size: 12; font-weight: normal">Mengetahui,</div>
        </div>
        <table style="width: 100%;">
            <tbody>
                <tr style="width: 50%;">
                    <td style="width: 50%; text-align: center">{{ $reportData['mengetahui1_jabatan'] }}</td>
                    <td style="width: 50%;  text-align: center">{{ $reportData['mengetahui2_jabatan'] }}</td>
                </tr>
                <tr style="width: 50%;">
                    <td style="padding: 3rem 0; width: 50%;"></td>
                    <td style="padding: 3rem 0; width: 50%;"></td>
                </tr>
                <tr style="width: 50%;">
                    <td style="width: 50%;  text-align: center; font-weight: bold; text-decoration: underline">{{$reportData['mengetahui1_nama']}}</td>
                    <td style="width: 50%;  text-align: center; font-weight: bold; text-decoration: underline">{{$reportData['mengetahui2_nama']}}</td>
                </tr>
                <tr style="width: 50%;">
                    <td style="width: 50%;  text-align: center"> NIP. {{$reportData['mengetahui1_nip']}}</td>
                    <td style="width: 50%;  text-align: center">NIP. {{$reportData['mengetahui2_nip']}}</td>
                </tr>
            </tbody>
        </table>
        <div class="subtitle-wrapper" style="margin-top: 200px">
            <div class="subtitle" style="font-size: 14">
                UNIT PENJAMINAN MUTU
            </div>
            <div class="subtitle" style="font-size: 14">
                {{$fakultas != 'Universitas' ? $fakultas : 'UNIVERSITAS NEGERI GORONTALO'}}
            </div>
            <div class="subtitle" style="font-size: 14">
                {{$fakultas != 'Universitas' ? 'UNIVERSITAS NEGERI GORONTALO' : ''}}
            </div>
            <div class="subtitle" style="font-size: 14">
                {{$rtm->tahun}}
            </div>
        </div>
    </div>

</body>

</html>
