<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BAB I - Laporan Survei </title>
    <style>
        @page {
            margin-top: 2cm;
            margin-bottom: 2cm;
            margin-left: 2cm;
            margin-right: 2cm;
        }

        body {
            font-family: 'Helvetica', sans-serif;
            margin: 0;
        }

        * {
            font-size: 12;
        }

        .heading-1 {
            text-align: center;
        }

        .heading-2 {
            margin-bottom: 0;
        }

        .kop {
            border-bottom: 1px solid black;
        }

        .audit-detail {
            margin: 0 auto;
            margin-bottom: 20px;
        }

        .audit-detail th,
        .audit-detail td {
            text-align: left;
            vertical-align: top;
        }

        .audit-detail th {
            padding-right: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
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
            line-height: 1.5em;
        }

        .number-list {
            list-style-type: decimal;
            margin-left: 25px;
            padding: 0px;
            line-height: 2em;
        }

        .number-list li {
            text-align: justify;
        }

        .ttd {
            width: 100%;
        }

        .ttd td {
            width: 33.33%;
            text-align: center;
        }

        h6 {
            margin-bottom: 0px;
        }
        
        /* Rich Text Editor Content Styling */
        .paragraf p {
            text-align: justify;
            margin-bottom: 10px;
            text-indent: 18px;
        }
        
        .paragraf ul, .paragraf ol,
        .agenda-kegiatan ul, .agenda-kegiatan ol,
        .peserta-container ul, .peserta-container ol {
            padding-left: 20px;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        
        .paragraf ul li, .agenda-kegiatan ul li, .peserta-container ul li {
            list-style-type: disc;
            margin-bottom: 5px;
            text-align: justify;
        }
        
        .paragraf ol li, .agenda-kegiatan ol li, .peserta-container ol li {
            list-style-type: decimal;
            margin-bottom: 5px;
            text-align: justify;
        }
        
        .agenda-kegiatan p, .peserta-container p {
            margin-bottom: 10px;
        }
        
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 15px;
        }
        
        table td, table th {
            border: 1px solid #ddd;
            padding: 8px;
        }
        
        blockquote {
            border-left: 3px solid #ccc;
            margin-left: 10px;
            padding-left: 15px;
            color: #555;
            font-style: italic;
        }
    </style>
</head>

<body>
    <h5 class="heading-1">
        LAPORAN
        <br>
        {{ $rtm->name }}
        <br>
        TAHUN AKADEMIK {{ $reportData['tahun_akademik'] ?? '2022/2023' }}
    </h5>
    <h6>
        A. TUJUAN
    </h6>
    @if(!empty($reportData['tujuan']))
        <div class="paragraf">
            {!! $reportData['tujuan'] !!}
        </div>
    @else
    <p class="paragraf">
        Rapat Tinjauan Manajmen (RTM) {{ $fakultas != 'Universitas' ? $fakultas : 'Universitas Negeri Gorontalo' }} adalah pertemuan yang dilakukan
        oleh pimpinan di lingkungan {{ $fakultas != 'Universitas' ? $fakultas : 'Universitas Negeri Gorontalo' }} secara periodik minimal 1 tahun sekali yang
        merupakan implementasi pelaksanaan siklus SPMI yaitu siklus Pengendalian yang
        bertujuan untuk mengevaluasi kinerja system secara menyeluruh. Namun pada kesempatan
        ini RTM dilaksankan untuk menyampaian garis-garis besar hasil evaluasi pelaksanaan
        penjaminan mutu di {{ $fakultas != 'Universitas' ? $fakultas : 'Universitas Negeri Gorontalo' }} dintaranya akan membahas:
    </p>
    <p class="paragraf">
    <ul class="paragraf" style="list-style: disc">
        <li class="paragraf">Hasil Audit Mutu Internal tahun {{ $rtm->tahun }}</li>
        <li class="paragraf" style="margin-top: 8px">Hasil umpan balik dari stakeholder</li>
    </ul>
    </p>
    <p class="paragraf">
        Permasalahan manajemen system penjaminan mutu internal untuk meninjau kinerja system
        manajemen mutu, dan kinerja pelayanan atau upaya {{ $fakultas != 'Universitas' ? $fakultas : 'Universitas Negeri Gorontalo' }} guna memastikan
        kelanjutan, kesesuaian, kecukupan, dan efektifitas system manajemen mutu.
    </p>
    @endif
    <h6>
        B. PELAKSANAAN
    </h6>
    <p>
        Rapat Tinjauan Manajemen (RTM) dilaksanakan pada:
    </p>
    <table style="width: 100%;">
        <tbody>
            <tr style="width: 50%;">
                <td style="width: 30%; vertical-align: top">Hari / Tanggal</td>
                <td style="width: 5%; vertical-align: top">:</td>
                <td style="width: 65%; vertical-align: top">{{ !empty($reportData['tanggal_pelaksanaan']) ? date('l / d F Y', strtotime($reportData['tanggal_pelaksanaan'])) : "Jum'at / 17 Maret 2023" }}</td>
            </tr>
            <tr style="width: 50%;">
                <td style="width: 30%; vertical-align: top">Waktu</td>
                <td style="width: 5%; vertical-align: top">:</td>
                <td style="width: 65%; vertical-align: top">{{ !empty($reportData['waktu_pelaksanaan']) ? date('H:i', strtotime($reportData['waktu_pelaksanaan'])) : '08.30' }} – {{ !empty($reportData['waktu_pelaksanaan']) ? date('H:i', strtotime('+3 hours', strtotime($reportData['waktu_pelaksanaan']))) : '11.30' }}</td>
            </tr>
            <tr style="width: 50%;">
                <td style="width: 30%; vertical-align: top">Tempat</td>
                <td style="width: 5%; vertical-align: top">:</td>
                <td style="width: 65%; vertical-align: top">{{ $reportData['tempat_pelaksanaan'] ?? 'Century Beach Resort' }}</td>
            </tr>
            <tr style="width: 50%;">
                <td style="width: 30%; vertical-align: top">Agenda Rapat</td>
                <td style="width: 5%; vertical-align: top">:</td>
                <td style="width: 65%; vertical-align: top">{{ $reportData['agenda'] ?? 'Hasil AMI 2022' }}</td>
            </tr>
            <tr style="width: 50%;">
                <td style="width: 30%; vertical-align: top">Pimpinan Rapat</td>
                <td style="width: 5%; vertical-align: top">:</td>
                <td style="width: 65%; vertical-align: top">{{ $reportData['pemimpin_rapat'] ?? 'Dekan' }}</td>
            </tr>
            <tr style="width: 50%;">
                <td style="width: 30%; vertical-align: top">Peserta</td>
                <td style="width: 5%; vertical-align: top">:</td>
                <td style="width: 65%; vertical-align: top">
                    @if(!empty($reportData['peserta']))
                        <div class="peserta-container">
                            {!! $reportData['peserta'] !!}
                        </div>
                    @else
                        <ul style="list-style: disc">
                            <li>Wakil Dekan</li>
                            <li style="margin-top: 8px">Ketua Jurusan</li>
                            <li style="margin-top: 8px">Sekretaris Jurusan</li>
                            <li style="margin-top: 8px">Ketua Jurusan</li>
                            <li style="margin-top: 8px">Ketua Jurusan</li>
                            <li style="margin-top: 8px">Ketua Program Studi</li>
                            <li style="margin-top: 8px">Kepala Laboratorium</li>
                        </ul>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
    <p>
        Agenda Kegiatan:
    </p>
    @if(!empty($reportData['agenda_kegiatan']))
        <div class="agenda-kegiatan">
            {!! $reportData['agenda_kegiatan'] !!}
        </div>
    @else
        <ol class="paragraf">
            <li class="paragraf">Pembukaan</li>
            <li class="paragraf" style="margin-top: 8px">Doa</li>
            <li class="paragraf" style="margin-top: 8px">Sambutan / arahan dari pimpinan</li>
            <li class="paragraf" style="margin-top: 8px">Tinjauan terhadap Hasil RTM tahun lalu</li>
            <li class="paragraf" style="margin-top: 8px">Pembahasan hasil audit mutu internal</li>
        </ol>
    @endif
    <h6>
        C. HASIL
    </h6>
    <div class="paragraf">
    @if(!empty($reportData['hasil']))
        {!! $reportData['hasil'] !!}
    @else
    <ul class="paragraf" style="list-style: disc">
        <li class="paragraf">RTM dihadiri 33 peserta termasuk yang mewakili seluruh UPPS</li>
        <li class="paragraf" style="margin-top: 8px">Hasil kegiatan audit pada tahun {{ $rtm->tahun }} </li>
    </ul>
    @endif
    </div>
    
    @if(isset($ami_data_by_period) && count($ami_data_by_period) > 0)
    <p class="paragraf">
        Detail hasil Audit Mutu Internal dari berbagai periode dapat dilihat pada lampiran laporan ini.
    </p>
    @endif
    
    <h6>
        D. KESIMPULAN
    </h6>
    <div class="paragraf">
    @if(!empty($reportData['kesimpulan']))
        {!! $reportData['kesimpulan'] !!}
    @else
    <ul class="paragraf" style="list-style: disc">
        <li class="paragraf">Seluruh hasil temuan audit dan permasalahan manajemen lainnya telah dipaparkan dan
            telah ditunjuk penanggungjawab untuk melaksanakan tindak lanjut</li>
        <li class="paragraf" style="margin-top: 8px">Setiap tindak lanjut akan dilaporkan kepada {{ $fakultas != 'Universitas' ? 'Dekan' : 'Rektor' }}.</li>
    </ul>
    @endif
    </div>
    <h6>
        E. PENUTUP
    </h6>
    @if(!empty($reportData['penutup']))
        <div class="paragraf">
            {!! $reportData['penutup'] !!}
        </div>
    @else
    <p class="paragraf">
        Demikian laporan RTM tahun {{ $rtm->tahun }} ini dibuat untuk digunakan sebagai data dukung dokumen
        pelaksanaan SPMI untuk mencapai Visi Misi UNG
    </p>
    @endif

</body>

</html>
