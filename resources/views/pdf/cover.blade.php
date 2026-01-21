<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cover - Laporan Survei</title>
    <style>
        @page {
            margin: 2cm;
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
            width: 8rem;
            margin-top: 120px
        }
    </style>
</head>

<body>
    <div class="container" style="display: flex; justify-content: space-between; height: 100%;">
        <div class="title-wrapper" style="margin-bottom: 120px">
            <div class="title">LAPORAN</div>
            <div class="title">{{ $rtm->name }}</div>
            <div class="title">{{ $fakultas != 'Universitas' ? $fakultas : '' }}</div>
            @if ($prodi != "Prodi")
                <div class="title">{{ $prodi }}</div>
            @endif
            <div class="title">TAHUN {{ $reportData['tahun_akademik'] ?? '2022/2023' }}</div>
        </div>
        <img class="logo" src="{{ public_path('images/ung.png') }}" alt="Logo Universitas"
            style="margin-bottom: 120px">
        <div class="subtitle-wrapper">
            <div class="subtitle">
                UNIT PENJAMINAN MUTU
            </div>
            <div class="subtitle">
                {{ $fakultas != 'Universitas' ? $fakultas : '' }}
                @if ($prodi != "Prodi")
                    {{ $prodi }}
                @endif
            </div>
            <div class="subtitle">
                UNIVERSITAS NEGERI GORONTALO
            </div>
            <div class="subtitle">
                {{ $rtm->tahun }}
            </div>

        </div>
    </div>
</body>

</html>
