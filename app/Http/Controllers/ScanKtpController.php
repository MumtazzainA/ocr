<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use thiagoalessio\TesseractOCR\TesseractOCR;

class ScanKtpController extends Controller
{
    public function showScanForm()
    {
        return view('scan_ktp');
    }

    public function processScan(Request $request)
    {
        // Proses pemindaian KTP di sini
        // $request->file('ktp_image') berisi file gambar KTP yang diunggah oleh pengguna

        // Lakukan validasi bahwa yang diunggah adalah file gambar KTP, misalnya menggunakan ekstensi file
        $validatedData = $request->validate([
            'ktp_image' => 'required|image',
        ]);
        $imagePath = $validatedData['ktp_image']->path();;

        // Menjalankan pemindaian dengan Tesseract OCR menggunakan parameter yang telah diatur
        $result = (new TesseractOCR($imagePath))

            ->lang('ind') // Menggunakan bahasa Indonesia dan Inggris
            ->run();


        // Proses pemindaian KTP
        // ->executable('/usr/bin/tesseract') // Ganti dengan path Tesseract OCR di sistem Anda

        // Praproses gambar menggunakan teknik pengolahan gambar seperti penghalusan dan segmentasi

        // Gunakan Tesseract OCR untuk memindai teks pada gambar KTP

        // Pembersihan teks hasil pemindaian
        // $cleanedResult = $this->cleanScanResult($result);
        $data=$this->processText($result);

        // Tampilkan hasil pemindaian
        // $nama = $this->parseNama($result);

        // // // Parsing nilai nik
        // $nik = $this->parseNIK($result);
        // $tgl=$this->parseTempatTanggalLahir($result);
        // $alm=$this->parseAlamat($result);
        // $rt=$this->parseRTAW($result);
        // $nama = $this->findData($result, 'Nama');
        // $nik = $this->findData($result, 'NIK');
        // $tgl = $this->findData($result, 'Tempat/Tgi Lahir');

        // // // Tampilkan hasil pemindaian
        

        return view('scan_result', ['result' => $result]);
        // return view('scan_result', compact('data'));

        // return view('scan_result', ['data' => $data]);
    }
    public function scanKTP(Request $request)
    {
        // Validate the uploaded KTP image file
    $validatedData = $request->validate([
        'ktp_image' => 'required|image',
    ]);
    $imagePath = $validatedData['ktp_image']->path();

    // Load the image using OpenCV
    $image = new Image($imagePath);

    // Convert the image to grayscale
    $grayImage = $image->cvtColor(Image::COLOR_BGR2GRAY);

    // Apply thresholding using OpenCV
    $thresholdValue = 127;
    $maxValue = 255;
    $thresholdedImage = $grayImage->threshold($thresholdValue, $maxValue, Image::THRESH_TRUNC);

    // Save the thresholded image to the server
    $thresholdedImagePath = 'ktp_images/thresholded_' . time() . '.png';
    $thresholdedImage->save(storage_path('app/' . $thresholdedImagePath));

    // Perform OCR on the thresholded image using Tesseract OCR
    $result = (new TesseractOCR(storage_path('app/' . $thresholdedImagePath)))
        ->lang('ind')
        ->run();

    // Process the OCR result
    $data = $this->processText($result);

    // Display the scan result
    return view('scan_result', ['data' => $data]);
    }


    private function cleanScanResult($result)
    {
        // Lakukan pembersihan teks hasil pemindaian di sini
        // Anda dapat menggunakan teknik pemrosesan teks seperti menghapus karakter yang tidak diinginkan atau membersihkan noise

        // Contoh: menghapus karakter non-alfanumerik
        $cleanedResult = preg_replace("/[^a-zA-Z0-9]+/", "", $result);

        return $cleanedResult;
    }
    private function parseKTPData($text)
    {
        // Array asosiatif untuk menyimpan data KTP yang diuraikan
        $ktpData = [];

        // Parsing Nama
        $nama = $this->findData($text, 'Nama');
        if ($nama) {
            $ktpData['nama'] = $nama;
        }

        // Parsing NIK
        $nik = $this->findData($text, 'NIK');
        if ($nik) {
            $ktpData['nik'] = $nik;
        }

        // Parsing Tempat/Tanggal Lahir
        $tglLahir = $this->findData($text, 'Tempat/Tgl Lahir');
        if ($tglLahir) {
            $ktpData['tgl_lahir'] = $tglLahir;
        }

        // Parsing Alamat
        $alamat = $this->findData($text, 'Alamat');
        if ($alamat) {
            $ktpData['alamat'] = $alamat;
        }

        // Parsing RT/RW
        $rtRw = $this->findData($text, 'RT/RW');
        if ($rtRw) {
            $ktpData['rt_rw'] = $rtRw;
        }

        // Parsing Golongan Darah
        $golDarah = $this->findData($text, 'Golongan Darah');
        if ($golDarah) {
            $ktpData['gol_darah'] = $golDarah;
        }

        // Parsing data lainnya...

        return $ktpData;
    }
    private function findData($text, $keyword)
    {
        // Mencari data berdasarkan kata kunci
        $escapedKeyword = preg_quote($keyword, '/');
        $pattern = '/' . $escapedKeyword . '(.+)/';
        preg_match($pattern, $text, $matches);
        if (isset($matches[1])) {
            return trim($matches[1]);
        }
        return null;
    }
    private function processText($text)
    {
        $lines = explode("\n", $text);

        $data = [
            'name' => '',
            'nik' => '',
            'birth_place_date' => '',
            'address' => '',
            'rt_rw' => '',
            'blood_type' => ''
        ];

        foreach ($lines as $line) {
            if (strpos($line, 'Nama') !== false) {
                $data['name'] = trim(str_replace('nam:', '', $line));
            } elseif (strpos($line, 'NIK') !== false) {
                $data['nik'] = trim(str_replace('nik:', '', $line));
            } elseif (strpos($line, 'Tempat Tgl lahir') !== false) {
                $data['birth_place_date'] = trim(str_replace('Tempat Tgl', '', $line));
            } elseif (strpos($line, 'alamat') !== false) {
                $data['address'] = trim(str_replace('alamat', '', $line));
            } elseif (strpos($line, 'RT/Re:') !== false) {
                $data['rt_rw'] = trim(str_replace('RT/Re:', '', $line));
            } elseif (strpos($line, 'golongan darah:') !== false) {
                $data['blood_type'] = trim(str_replace('golongan darah:', '', $line));
            }

            // Add processing for other KTP information as needed
        }

        return $data;
    }
   
    public function parseNama($scanResult)
    {
        $regex = '/Nama\s+“\s*([A-Z"]+)/';
        preg_match($regex, $scanResult, $matches);

        if (isset($matches[1])) {
            $nama = trim($matches[1]);
            return "Nama : $nama";
        }

        return null; // Jika pola nama tidak ditemukan, kembalikan nilai null
    }

    public function parseNIK($scanResult)
    {
        $regex = '/NIK\s*:\s*([0-9]+)/';
        preg_match($regex, $scanResult, $matches);

        if (isset($matches[1])) {
            $nik = $matches[1];
            return "NIK : $nik";
        }

        return null; // Jika pola NIK tidak ditemukan, kembalikan nilai null
    }

    public function parseTempatTanggalLahir($scanResult)
    {
        $regex = '/Tempat\/Tgi Lahir : ([A-Z]+), (\d{2}-\d{2}-\d{4})/';
        preg_match($regex, $scanResult, $matches);

        if (isset($matches[1]) && isset($matches[2])) {
            $tempatLahir = $matches[1];
            $tanggalLahir = $matches[2];

            return "Tempat Lahir/Tgl Lahir:$tempatLahir,$tanggalLahir";
        }

        return null; // Jika pola tempat dan tanggal lahir tidak ditemukan, kembalikan nilai null
    }
    public function parseAlamat($scanResult)
    {
        $regex = '/Alamat\s([^\n]+)/';
        preg_match($regex, $scanResult, $matches);

        if (isset($matches[1])) {
            $alamat = trim($matches[1]);
            $alamat = str_replace('J ', 'JL. ', $alamat);
            return "Alamat : $alamat";
        }

        return null; // Jika pola alamat tidak ditemukan, kembalikan nilai null
    }
    public function parseRTAW($scanResult)
    {
        $regex = '/RTAW\s(\d+)\s\/\s(\d+)/';
        preg_match($regex, $scanResult, $matches);

        if (isset($matches[1]) && isset($matches[2])) {
            $rt = $matches[1];
            $rw = $matches[2];
            return "RT/RW : $rt / $rw";
        }

        return null; // Jika pola RTAW tidak ditemukan, kembalikan nilai null
    }
}
