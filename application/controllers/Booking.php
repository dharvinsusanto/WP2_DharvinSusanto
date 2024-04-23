public function tambahBooking()
{
    $id_buku = $this->uri->segment(3);
    // Memilih data buku yang untuk dimasukkan ke tabel temp/keranjang melalui variabel $isi
    $d = $this->db->query("SELECT * FROM buku WHERE id='$id_buku'")->row();
    // Berupa data yang akan disimpan ke dalam tabel temp/keranjang
    $isi = [
        'id_buku' => $id_buku,
        'judul_buku' => $d->judul_buku,
        'id_user' => $this->session->userdata('id_user'),
        'email_user' => $this->session->userdata('email'),
        'tgl_booking' => date('Y-m-d H:i:s'),
        'image' => $d->image,
        'penulis' => $d->pengarang,
        'penerbit' => $d->penerbit,
        'tahun_terbit' => $d->tahun_terbit
    ];
    // Cek apakah buku yang diklik booking sudah ada di keranjang
    $temp = $this->ModelBooking->getDataWhere('temp', ['id_buku' => $id_buku])->num_rows();
    $userid = $this->session->userdata('id_user');
    // Cek jika sudah memasukkan 3 buku untuk dibooking dalam keranjang
    $tempuser = $this->db->query("SELECT * FROM temp WHERE id_user ='$userid'")->num_rows();
    // Cek jika masih ada booking buku yang belum diambil
    $databooking = $this->db->query("SELECT * FROM booking WHERE id_user='$userid'")->num_rows();
    if ($databooking > 0) {
        $this->session->set_flashdata('pesan', '<div class="alert alert-danger alert-message" role="alert">Masih Ada booking buku sebelumnya yang belum diambil.<br> Ambil Buku yang dibooking atau tunggu 1x24 Jam untuk bisa booking kembali </div>');
        redirect(base_url());
    }
    // Jika buku yang diklik booking sudah ada di keranjang
    if ($temp > 0) {
        $this->session->set_flashdata('pesan', '<div class="alert alert-danger alert-message" role="alert">Buku ini Sudah anda booking </div>');
        redirect(base_url() . 'home');
    }
    // Jika buku yang akan dibooking sudah mencapai 3 item
    if ($tempuser == 3) {
        $this->session->set_flashdata('pesan', '<div class="alert alert-danger alert-message" role="alert">Booking Buku Tidak Boleh Lebih dari 3</div>');
        redirect(base_url() . 'home');
    }
    // Membuat tabel temp jika belum ada
    $this->ModelBooking->createTemp();
    $this->ModelBooking->insertData('temp', $isi);
    // Pesan ketika berhasil memasukkan buku ke keranjang
    $this->session->set_flashdata('pesan', '<div class="alert alert-success alert-message" role="alert">Buku berhasil ditambahkan ke keranjang </div>');
    redirect(base_url() . 'home');
}

public function hapusbooking()
 {
 $id_buku = $this->uri->segment(3);
 $id_user = $this->session->userdata('id_user');
 
 $this->ModelBooking->deleteData(['id_buku' => $id_buku], 'temp');
 $kosong = $this->db->query("select*from temp where id_user='$id_user'")-
>num_rows();
 if ($kosong < 1) {
 $this->session->set_flashdata('pesan', '<div class="alert alertmassege alert-danger" role="alert">Tidak Ada Buku dikeranjang</div>');
 redirect(base_url());
 } else {
 redirect(base_url() . 'booking');
 }
 }
