import { describe, expect, it } from 'vitest'
import { ringkasanTugas, statusTugas } from './status'

describe('statusTugas', () => {
  it('mengembalikan "Selesai" untuk tugas yang selesai', () => {
    expect(statusTugas(true)).toBe('Selesai')
  })

  it('mengembalikan "Belum selesai" untuk tugas yang belum', () => {
    expect(statusTugas(false)).toBe('Belum selesai')
  })
})

describe('ringkasanTugas', () => {
  it('menghitung total, selesai, dan belum selesai dengan benar', () => {
    const daftar = [
      { selesai: true },
      { selesai: false },
      { selesai: true },
    ]

    expect(ringkasanTugas(daftar)).toEqual({
      total: 3,
      selesai: 2,
      belum: 1,
    })
  })

  it('mengembalikan nol semua untuk daftar kosong', () => {
    expect(ringkasanTugas([])).toEqual({ total: 0, selesai: 0, belum: 0 })
  })
})
