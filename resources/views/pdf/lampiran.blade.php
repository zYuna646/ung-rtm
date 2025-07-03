<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lampiran RTM - AMI</title>
    <style>
        @page {
            margin-top: 2cm;
            margin-bottom: 2cm;
            margin-left: 2cm;
            margin-right: 2cm;
            size: landscape;
        }

        body {
            font-family: 'Helvetica', sans-serif;
            margin: 0;
            font-size: 11pt;
        }

        h1,
        h2,
        h3 {
            margin-top: 24pt;
            margin-bottom: 6pt;
        }

        h4,
        h5,
        h6 {
            margin-top: 12pt;
            margin-bottom: 6pt;
        }

        h1 {
            font-size: 16pt;
            text-align: center;
        }

        h2 {
            font-size: 14pt;
        }

        .heading-1 {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 24pt;
        }

        .heading-2 {
            font-size: 14pt;
            font-weight: bold;
            margin-top: 24pt;
            margin-bottom: 12pt;
        }

        .heading-3 {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 18pt;
            margin-bottom: 10pt;
        }

        .table-container {
            margin-top: 12pt;
            margin-bottom: 24pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9pt; /* Slightly reduce font size for better fitting */
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
            font-size: 10pt;
        }

        table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .category-header {
            background-color: #e0e0e0;
            font-weight: bold;
            font-size: 11pt;
            padding: 8px;
            text-align: left;
            border: 1px solid #000;
            border-bottom: 2px solid #000;
        }

        .page-break {
            page-break-after: always;
        }

        .no-page-break {
            page-break-inside: avoid;
        }

        .ami-period {
            margin-bottom: 10pt;
            padding: 8pt;
            background-color: #f5f5f5;
            border-radius: 5pt;
            border-left: 4pt solid #808080;
        }

        .period-header {
            background-color: #808080;
            color: white;
            font-weight: bold;
            font-size: 13pt;
            padding: 10px;
            margin-top: 30px;
            margin-bottom: 15px;
            text-align: center;
        }

        .section-title {
            margin-top: 40px;
            margin-bottom: 20px;
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
            border-bottom: 1px solid #808080;
            padding-bottom: 10px;
        }

        .timestamp-note {
            font-size: 9pt;
            color: #666;
            text-align: right;
            font-style: italic;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .warning-level {
            padding: 3px 6px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 9pt;
            text-align: center;
        }

        .warning-level-normal {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .warning-level-warning {
            background-color: #fff8e1;
            color: #f57f17;
        }

        .warning-level-danger {
            background-color: #ffebee;
            color: #c62828;
        }

        .category-average {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: right;
            border-top: 1px solid #a0a0a0;
        }

        .overall-average {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: right;
            border-top: 2px solid #000;
        }

        .amt-portrait {
            size: portrait;
        }

        .image-gallery {
            margin: 30px 0;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .image-item {
            page-break-inside: avoid;
            margin-bottom: 20px;
        }

        .image-container {
            border: 1px solid #ddd;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 5px;
        }

        .image-container img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .image-caption {
            margin-top: 10px;
            text-align: center;
            font-size: 10pt;
            font-style: italic;
            color: #333;
        }

        @page attachments {
            size: portrait;
        }

        .attachment-section {
            page: attachments;
        }
    </style>
</head>

<body>
    <h1 class="heading-1">
        LAMPIRAN {{ $rtm->name }}
    </h1>
    @if ($fakultas == 'Universitas')
        @if (isset($universitas_data) && !empty($universitas_data))
            <div class="section-title">Akreditasi Universitas</div>
            <div class="timestamp-note">Data akreditasi terpantau pada tanggal {{ now()->format('d F Y') }}</div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 4%;">No</th>
                            <th style="width: 10%;">Terakreditasi</th>
                            <th style="width: 25%;">No. Sertifikat</th>
                            <th style="width: 15%;">Tanggal Akreditasi</th>
                            <th style="width: 15%;">Tanggal Kadaluarsa</th>
                            <th style="width: 15%;">Batas Berlaku</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (is_array($universitas_data))
                            @foreach ($universitas_data as $index => $univ)
                                <tr>
                                    <td style="text-align: center;">{{ $index + 1 }}</td>
                                    <td style="text-align: center;">{{ $univ['status']['status_nama'] ?? '-' }}</td>
                                    <td>{{ $univ['akre_sk'] ?? '-' }}</td>
                                    <td style="text-align: center;">{{ $univ['akre_tglmulai'] ?? '-' }}</td>
                                    <td style="text-align: center;">{{ $univ['akre_tglakhir'] ?? '-' }}</td>
                                    <td style="text-align: center;">{{ $univ['batas_berlaku'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td style="text-align: center;">1</td>
                                <td style="text-align: center;">{{ $universitas_data['status']['status_nama'] ?? '-' }}
                                </td>
                                <td>{{ $universitas_data['akre_sk'] ?? '-' }}</td>
                                <td style="text-align: center;">{{ $universitas_data['akre_tglmulai'] ?? '-' }}</td>
                                <td style="text-align: center;">{{ $universitas_data['akre_tglakhir'] ?? '-' }}</td>
                                <td style="text-align: center;">{{ $universitas_data['batas_berlaku'] ?? '-' }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        @endif
    @endif


    @if (isset($akreditasi_data) && count($akreditasi_data) > 0)
        <div class="section-title">Data Akreditasi Program Studi</div>
        <div class="timestamp-note">Data akreditasi terpantau pada tanggal {{ now()->format('d F Y') }}</div>

        <div class="table-container">
                            <table>
                    <thead>
                        <tr>
                            <th style="width: 3%;">No</th>
                            <th style="width: 8%;">Fakultas</th>
                            <th style="width: 3%;">Jenjang</th>
                            <th style="width: 12%;">Program Studi</th>
                            <th style="width: 5%;">Akreditasi</th>
                            {{-- <th style="width: 12%;">No. Sertifikat</th>
                            <th style="width: 8%;">Tanggal Akreditasi</th>
                            <th style="width: 8%;">Tanggal Kadaluarsa</th>
                            <th style="width: 6%;">Batas Berlaku</th> --}}
                            <th style="width: 6%;">Peringatan</th>
                            <th style="width: 15%;">Analisis Masalah dan Pemecahannya</th>
                            <th style="width: 14%;">Target Penyelesaian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($akreditasi_data as $index => $akreditasi)
                            <tr>
                                <td style="text-align: center;">{{ $index + 1 }}</td>
                                <td>{{ $akreditasi['prodi']['fakultas']['fakultas_alias'] }}</td>
                                <td style="text-align: center;">{{ $akreditasi['jenjang']['jenjang_alias'] }}</td>
                                <td>{{ $akreditasi['prodi']['prodi_nama'] }}</td>
                                <td style="text-align: center;">{{ $akreditasi['status']['status_nama'] }}</td>
                                {{-- <td>{{ $akreditasi['akre_sk'] }}</td>
                                <td style="text-align: center;">{{ $akreditasi['akre_tglmulai'] }}</td>
                                <td style="text-align: center;">{{ $akreditasi['akre_tglakhir'] }}</td>
                                <td style="text-align: center;">{{ $akreditasi['batas_berlaku'] }}</td> --}}
                                <td>
                                    <div class="warning-level warning-level-{{ $akreditasi['peringatan_level'] }}">
                                        {{ $akreditasi['peringatan'] }}
                                    </div>
                                </td>
                                <td>{{ $akreditasi['rencana_tindak_lanjut'] ?? '-' }}</td>
                                <td>{{ $akreditasi['target_penyelesaian'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                </tbody>
            </table>
        </div>
    @elseif (!isset($universitas_data) || empty($universitas_data))
        <div style="text-align: center; margin-top: 40px; margin-bottom: 40px;">
            <p>Tidak ada data Akreditasi untuk ditampilkan.</p>
        </div>
    @endif

    <div class="page-break"></div>
    <div class="amt-portrait">
        @if (isset($ami_data_by_period) && count($ami_data_by_period) > 0)
            <div class="section-title">Audit Mutu Internal (AMI)</div>

            @foreach ($ami_data_by_period as $periodName => $amiData)
                <div class="period-header">{{ $periodName }}</div>

                @if (isset($amiData['categories']) && count($amiData['categories']) > 0)
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 40%;">Pernyataan Standar</th>
                                    <th style="width: 5%;">Sesuai</th>
                                    <th style="width: 5%;">Tidak Sesuai</th>
                                    <th style="width: 10%;">Capaian Kinerja</th>
                                    <th style="width: 15%;">Analisis Masalah dan Pemecahannya</th>
                                    <th style="width: 15%;">Target Penyelesaian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($amiData['categories'] as $category => $indicators)
                                    <tr>
                                        <td colspan="8" class="category-header">{{ $category }}</td>
                                    </tr>
                                    @foreach ($indicators as $indicator)
                                        <tr>
                                            <td style="text-align: center;">{{ $indicator['code'] }}</td>
                                            <td>{{ $indicator['desc'] }}</td>
                                            <td style="text-align: center;">
                                                @if(is_array($indicator['sesuai']))
                                                    {{ count($indicator['sesuai']) }}
                                                @else
                                                    0
                                                @endif
                                            </td>
                                            <td style="text-align: center;">
                                                @if(is_array($indicator['tidak_sesuai']))
                                                    {{ count($indicator['tidak_sesuai']) }}
                                                @else
                                                    0
                                                @endif
                                            </td>
                                            <td style="text-align: center;">{{ $indicator['score'] }}%</td>
                                            <td>
                                                @if (isset($indicator['rencana_tindak_lanjut']))
                                                    {{ $indicator['rencana_tindak_lanjut'] }}
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($indicator['target_penyelesaian']))
                                                    {{ $indicator['target_penyelesaian'] }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    <!-- Category Average Row -->
                                    <tr>
                                        <td colspan="5" class="category-average">Rata-rata {{ $category }}:</td>
                                        <td style="text-align: center; font-weight: bold; background-color: #f0f0f0; border-top: 1px solid #a0a0a0;">{{ $amiData['category_averages'][$category] }}%</td>
                                        <td colspan="2" style="background-color: #f0f0f0; border-top: 1px solid #a0a0a0;"></td>
                                    </tr>
                                @endforeach
                                <!-- Overall Average Row -->
                                <tr>
                                    <td colspan="5" class="overall-average">Rata-rata Keseluruhan:</td>
                                    <td style="text-align: center; font-weight: bold; background-color: #e0e0e0; border-top: 2px solid #000;">{{ $amiData['overall_average'] }}%</td>
                                    <td colspan="2" style="background-color: #e0e0e0; border-top: 2px solid #000;"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="text-align: center; margin: 20px 0;">
                        <p>Tidak ada data AMI untuk periode ini.</p>
                    </div>
                @endif
            @endforeach
        @else
            <div style="text-align: center; margin-top: 40px; margin-bottom: 40px;">
                <p>Tidak ada data AMI untuk ditampilkan.</p>
            </div>
        @endif

        @if (isset($survei_data_by_period) && count($survei_data_by_period) > 0)
            <div class="section-title">Hasil Survei Kepuasan</div>

            @foreach ($survei_data_by_period as $periodName => $surveiData)
                <div class="period-header">{{ $periodName }}</div>

                @if (count($surveiData) > 0)
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 40%;">Indikator</th>
                                    <th style="width: 10%;">Nilai Butir</th>
                                    <th style="width: 10%;">IKM</th>
                                    <th style="width: 17.5%;">Analisis Masalah dan Pemecahannya</th>
                                    <th style="width: 17.5%;">Target Penyelesaian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($surveiData as $index => $indicator)
                                    <tr>
                                        <td style="text-align: center;">{{ $index + 1 }}</td>
                                        <td>{{ $indicator['name'] }}</td>
                                        <td style="text-align: center;">{{ $indicator['nilai_butir'] }}</td>
                                        <td style="text-align: center;">{{ number_format($indicator['ikm'], 2) }}%</td>
                                        <td>
                                            @if (isset($indicator['rencana_tindak_lanjut']))
                                                {{ $indicator['rencana_tindak_lanjut'] }}
                                            @endif
                                        </td>
                                        <td>
                                            @if (isset($indicator['target_penyelesaian']))
                                                {{ $indicator['target_penyelesaian'] }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="text-align: center; margin: 20px 0;">
                        <p>Tidak ada data Survei untuk periode ini.</p>
                    </div>
                @endif
            @endforeach
        @else
            <div style="text-align: center; margin-top: 40px;">
                <p>Tidak ada data Survei untuk ditampilkan.</p>
            </div>
        @endif
    </div>



    @if ($lampiran->count() > 0)
        <div class="page-break"></div>
        <div class="attachment-section">
            <div class="section-title">Lampiran Gambar</div>

            <div class="image-gallery">
                @foreach ($lampiran as $image)
                    <div class="image-item">
                        <div class="image-container">
                            <img src="{{ public_path('storage/' . $image->file_path) }}" alt="{{ $image->judul }}">
                            <div class="image-caption">
                                {{ $image->judul }} ({{ $image->file_name }})
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</body>

</html>
