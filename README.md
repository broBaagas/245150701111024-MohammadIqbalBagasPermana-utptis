# UTP TIS kelas A
### Mohammad Iqbal Bagas Permana_245150701111024_UTP

| Method | Endpoint          | Deskripsi Singkat                             | Request Body                             | Response                                                                 |
| ------ | ----------------- | --------------------------------------------- | ---------------------------------------- | ------------------------------------------------------------------------ |
| GET    | `/api/items`      | Mengambil seluruh data barang dari file JSON  | -                                        | 200: Berhasil ambil data                                                 |
| GET    | `/api/items/{id}` | Mengambil 1 barang berdasarkan ID             | -                                        | 200: Data ditemukan<br>404: Item tidak ditemukan                         |
| POST   | `/api/items`      | Menambahkan barang baru                       | `kode_barang`, `name`, `price` (wajib)   | 201: Berhasil ditambahkan<br>422: Validasi gagal / kode duplikat         |
| PUT    | `/api/items/{id}` | Mengupdate seluruh data barang berdasarkan ID | `kode_barang`, `name`, `price` (wajib)   | 200: Berhasil update<br>404: Item tidak ditemukan<br>422: Validasi gagal |
| PATCH  | `/api/items/{id}` | Mengupdate sebagian data barang               | Opsional: `kode_barang`, `name`, `price` | 200: Berhasil patch<br>404: Item tidak ditemukan<br>422: Validasi gagal  |
| DELETE | `/api/items/{id}` | Menghapus barang berdasarkan ID               | -                                        | 200: Berhasil dihapus<br>404: Item tidak ditemukan                       |

## Dokumentasi Swagger
```http://127.0.0.1:8000/api/documentation```

<img width="1903" height="958" alt="image" src="https://github.com/user-attachments/assets/43bc76f3-d703-4c3b-a34b-a931232c60cb" />
