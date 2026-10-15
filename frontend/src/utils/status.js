/**
 * Logika murni aplikasi: menentukan label status tugas.
 * Dipisahkan dari komponen supaya bisa diuji unit test Vitest
 * tanpa Laravel atau browser.
 *
 * @param {boolean} selesai
 * @returns {string}
 */
export function statusTugas(selesai) {
  return selesai ? 'Selesai' : 'Belum selesai'
}

/**
 * Menghitung ringkasan daftar tugas.
 *
 * @param {Array<{selesai: boolean}>} daftar
 * @returns {{ total: number, selesai: number, belum: number }}
 */
export function ringkasanTugas(daftar) {
  const selesai = daftar.filter((t) => t.selesai).length
  return {
    total: daftar.length,
    selesai,
    belum: daftar.length - selesai,
  }
}
