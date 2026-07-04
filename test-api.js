async function getCuacaKetapang() {
  const url = "https://peta-maritim.bmkg.go.id/public_api/perairan";

  try {
    // Menambahkan Headers agar request dikenali seperti browser normal
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Accept': 'application/json'
      }
    });

    // 1. Validasi status HTTP code terlebih dahulu
    if (!response.ok) {
      throw new Error(`HTTP Error! Status: ${response.status}`);
    }

    // 2. Cek apakah response benar-benar berbentuk JSON sebelum di-parse
    const contentType = response.headers.get("content-type");
    if (!contentType || !contentType.includes("application/json")) {
      // Jika bukan JSON, kita ambil teks mentahnya untuk melihat pesan error dari server BMKG
      const rawText = await response.text();
      console.error("=== SERVER NOT RETURN JSON ===");
      console.error("Isi respons mentah dari server:\n", rawText.slice(0, 500)); // Batasi 500 karakter pertama
      return;
    }

    // 3. Jika aman, baru lakukan parsing JSON
    const result = await response.json();

    if (result.status === "success") {
      // Lakukan pencarian nama wilayah secara fleksibel (tidak case-sensitive)
      const dataKetapang = result.data.find(
        (wilayah) => wilayah.nama_wilayah.toLowerCase().includes("ketapang")
      );

      if (dataKetapang) {
        console.log("\n=== DATA CUACA MARITIM PERAIRAN KETAPANG ===");
        console.log(`Cuaca            : ${dataKetapang.cuaca}`);
        console.log(`Angin            : ${dataKetapang.arah_angin} (${dataKetapang.kecepatan_angin})`);
        console.log(`Tinggi Gelombang : ${dataKetapang.tinggi_gelombang}`);
        console.log(`Waktu Perbarui   : ${dataKetapang.waktu_update}\n`);
      } else {
        console.log("Wilayah Perairan Ketapang tidak ditemukan dalam daftar.");
      }
    } else {
      console.log("API merespons tetapi status gagal:", result.message);
    }

  } catch (error) {
    console.error("Gagal mengambil data dari API BMKG:", error.message);
  }
}

getCuacaKetapang();
