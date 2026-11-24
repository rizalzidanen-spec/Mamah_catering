let cart = [];

document.addEventListener('DOMContentLoaded', () => {
    // 1. Tangkap klik tombol tambah ke keranjang
    const cartButtons = document.querySelectorAll('.product-bottom a');

    cartButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productCard = this.closest('.product');
            if (!productCard) return;

            const name = productCard.querySelector('h3')?.innerText.trim() || 'Produk';
            const priceText = productCard.querySelector('strong')?.innerText.trim() || 'Rp 0';
            const price = parseInt(priceText.replace(/[^0-9]/g, '')) || 0;
            const image = productCard.querySelector('img')?.src || '';

            addToCart(name, price, image);
            openCart();
        });
    });

    updateCartBadge();
    renderCart();

    // 2. Indikator nama file Bukti Bayar saat dipilih pelanggan
    const inputBukti = document.getElementById('buktiBayar');
    if (inputBukti) {
        inputBukti.addEventListener('change', function() {
            const fileNameSpan = document.getElementById('namaFileBukti');
            if (fileNameSpan) {
                if (this.files && this.files[0]) {
                    fileNameSpan.innerText = 'File terpilih: ' + this.files[0].name;
                    fileNameSpan.style.color = '#16a34a'; // Warna hijau saat terisi
                    fileNameSpan.style.fontWeight = '600';
                } else {
                    fileNameSpan.innerText = 'Belum ada file dipilih';
                    fileNameSpan.style.color = '#64748b';
                    fileNameSpan.style.fontWeight = 'normal';
                }
            }
        });
    }
});

// ================= LOGIK KERANJANG =================
function addToCart(name, price, image) {
    const existing = cart.find(item => item.name === name);
    if (existing) {
        existing.qty += 1;
    } else {
        cart.push({ id: Date.now() + Math.random(), name, price, image, qty: 1 });
    }
    updateCartBadge();
    renderCart();
}

function changeCartQty(id, change) {
    const item = cart.find(p => p.id === id);
    if (!item) return;
    item.qty += change;
    if (item.qty <= 0) {
        cart = cart.filter(p => p.id !== id);
    }
    updateCartBadge();
    renderCart();
}

function setCartQty(id, value) {
    const item = cart.find(p => p.id === id);
    if (!item) return;
    let qty = parseInt(String(value).replace(/[^0-9]/g, ''), 10);
    if (!Number.isFinite(qty) || qty < 1) qty = 1;
    item.qty = qty;
    updateCartBadge();
    renderCart();
}

function removeFromCart(id) {
    cart = cart.filter(p => p.id !== id);
    updateCartBadge();
    renderCart();
}

function updateCartBadge() {
    const count = cart.reduce((total, item) => total + item.qty, 0);
    const badge = document.getElementById('cartCount');
    if (badge) {
        badge.innerText = count;
        badge.style.display = count > 0 ? 'flex' : 'none';
    }
}

function renderCart() {
    const container = document.getElementById('cartItems');
    const empty = document.getElementById('cartEmpty');
    const checkoutArea = document.getElementById('cartCheckoutArea');
    if (!container || !empty || !checkoutArea) return;

    if (cart.length === 0) {
        container.innerHTML = '';
        empty.style.display = 'block';
        checkoutArea.style.display = 'none';
        return;
    }

    empty.style.display = 'none';
    checkoutArea.style.display = 'block';

    container.innerHTML = cart.map(item => `
        <div class="cart-item-new">
            <img src="${item.image}" alt="${escapeHtml(item.name)}">
            <div class="cart-item-main">
                <h3>${escapeHtml(item.name)}</h3>
                <p>${formatRupiah(item.price)} / box</p>
                <div class="mini-qty">
                    <button type="button" onclick="changeCartQty(${item.id}, -1)">−</button>
                    <input type="number" value="${item.qty}" onchange="setCartQty(${item.id}, this.value)">
                    <button type="button" onclick="changeCartQty(${item.id}, 1)">+</button>
                    <button type="button" class="remove-item" onclick="removeFromCart(${item.id})"><i class="fa-solid fa-trash"></i></button>
                </div>
            </div>
            <b>${formatRupiah(item.price * item.qty)}</b>
        </div>
    `).join('');

    calculateCartTotal();
}

function calculateCartTotal() {
    const subtotal = cart.reduce((total, item) => total + (item.price * item.qty), 0);
    const totalQty = cart.reduce((total, item) => total + item.qty, 0);

    let discountPercent = 0;
    if (totalQty >= 200) discountPercent = 20;
    else if (totalQty >= 150) discountPercent = 15;
    else if (totalQty >= 100) discountPercent = 10;

    const discountAmount = subtotal * discountPercent / 100;
    const finalTotal = subtotal - discountAmount;

    document.getElementById('subtotalDisplay').innerText = formatRupiah(subtotal);
    document.getElementById('step1TotalDisplay').innerText = formatRupiah(finalTotal);
    document.getElementById('finalPriceDisplay').innerText = formatRupiah(finalTotal);

    const discountContainer = document.getElementById('discountContainer');
    if (discountPercent > 0) {
        discountContainer.style.display = 'flex';
        document.getElementById('discountLabel').innerText = `Diskon ${discountPercent}%:`;
        document.getElementById('discountDisplay').innerText = `- ${formatRupiah(discountAmount)}`;
    } else {
        discountContainer.style.display = 'none';
    }

    return { subtotal, finalTotal };
}

function formatRupiah(amount) {
    return 'Rp ' + Number(amount || 0).toLocaleString('id-ID');
}

function openCart() {
    renderCart();
    window.location.hash = 'cart';
}

function closeCart() {
    window.location.hash = '';
}

function goToStep1() {
    document.getElementById('step-1').style.display = 'block';
    document.getElementById('step-2').style.display = 'none';
    document.getElementById('step-3').style.display = 'none';
}

function goToStep2(e) {
    e.preventDefault();
    if (cart.length === 0) return alert('Keranjang masih kosong.');
    document.getElementById('step-1').style.display = 'none';
    document.getElementById('step-2').style.display = 'block';
}

let lastCreatedOrderId = '';

// ================= SIMPAN PESANAN KE DATABASE PHP =================
function goToStep3(e) {
    e.preventDefault();
    const selectedPayment = document.querySelector('input[name="paymentMethod"]:checked');
    if (!selectedPayment) return alert('Pilih metode pembayaran!');

    const totals = calculateCartTotal();
    const detailItems = cart.map(item => `${item.name} (${item.qty}x)`).join(', ');

    const formData = new FormData();
    formData.append('nama', document.getElementById('nama').value.trim());
    formData.append('telepon', document.getElementById('telepon').value.trim());
    formData.append('alamat', document.getElementById('alamat').value.trim());
    formData.append('kecamatan', document.getElementById('kecamatan').value.trim());
    formData.append('kelurahan', document.getElementById('kelurahan').value.trim());
    formData.append('kode_pos', document.getElementById('kodePos').value.trim());
    formData.append('tanggal_pesan', document.getElementById('tanggalPesanan').value);
    formData.append('catatan', document.getElementById('catatan').value.trim());
    formData.append('detail_pesanan', detailItems);
    formData.append('total_harga', totals.finalTotal);
    formData.append('metode_pembayaran', selectedPayment.value);

    fetch('proses_pesanan.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            lastCreatedOrderId = data.order_id;

            // 1. Set Nomor Pesanan & Tanggal
            document.getElementById('confirmOrderId').innerText = data.order_id;
            
            const now = new Date();
            const dateStr = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
            const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
            document.getElementById('confirmTanggalPesan').innerText = `${dateStr}, ${timeStr}`;

            // 2. Render Tabel Item Pesanan
            const tableBody = document.getElementById('confirmOrderItems');
            if (tableBody) {
                tableBody.innerHTML = cart.map(item => `
                    <tr>
                        <td>
                            <div class="tbl-menu-info">
                                <img src="${item.image}" alt="${escapeHtml(item.name)}" onerror="this.src='https://via.placeholder.com/50'">
                                <div>
                                    <strong>${escapeHtml(item.name)}</strong>
                                </div>
                            </div>
                        </td>
                        <td style="text-align:center;">${item.qty} box</td>
                        <td style="text-align:right;">${formatRupiah(item.price)}</td>
                        <td style="text-align:right; font-weight:600;">${formatRupiah(item.price * item.qty)}</td>
                    </tr>
                `).join('');
            }

            // 3. Render Total Pesanan di Kolom Kiri
            const totalQty = cart.reduce((sum, item) => sum + item.qty, 0);
            if (document.getElementById('confirmTotalItemsCount')) {
                document.getElementById('confirmTotalItemsCount').innerText = `${cart.length} menu (${totalQty} item)`;
            }
            if (document.getElementById('confirmLeftTotal')) {
                document.getElementById('confirmLeftTotal').innerText = formatRupiah(totals.finalTotal);
            }

            // 4. Render Metode Pembayaran Terpilih
            let payIcon = 'fa-qrcode';
            let payDesc = 'Scan kode QR melalui aplikasi bank atau e-wallet.';

            if (selectedPayment.value.includes('BCA')) {
                payIcon = 'fa-landmark';
                payDesc = 'No. Rekening: 123-456-7890 (a.n. Mamah Catering)';
            } else if (selectedPayment.value.includes('DANA')) {
                payIcon = 'fa-wallet';
                payDesc = 'No. DANA: 0812-3456-7890 (a.n. Mamah Catering)';
            } else if (selectedPayment.value.includes('GoPay')) {
                payIcon = 'fa-mobile-screen-button';
                payDesc = 'No. GoPay: 0812-3456-7890 (a.n. Mamah Catering)';
            }

            const confirmPaymentCard = document.getElementById('confirmPaymentCard');
            if (confirmPaymentCard) {
                confirmPaymentCard.innerHTML = `
                    <div class="selected-payment-card">
                        <div class="sp-left">
                            <div class="sp-icon"><i class="fa-solid ${payIcon}"></i></div>
                            <div>
                                <strong>${selectedPayment.value}</strong>
                                <small>${payDesc}</small>
                            </div>
                        </div>
                        <i class="fa-solid fa-circle-check sp-check"></i>
                    </div>
                `;
            }

            // 5. Render Ringkasan Pembayaran
            if (document.getElementById('confirmSubtotal')) {
                document.getElementById('confirmSubtotal').innerText = formatRupiah(totals.subtotal);
            }
            const discountAmount = totals.subtotal - totals.finalTotal;

            const confirmDiscountRow = document.getElementById('confirmDiscountRow');
            if (confirmDiscountRow) {
                if (discountAmount > 0) {
                    confirmDiscountRow.style.display = 'flex';
                    document.getElementById('confirmDiscount').innerText = `- ${formatRupiah(discountAmount)}`;
                } else {
                    confirmDiscountRow.style.display = 'none';
                }
            }

            if (document.getElementById('confirmFinalTotal')) {
                document.getElementById('confirmFinalTotal').innerText = formatRupiah(totals.finalTotal);
            }

            // Tampilkan Step 3 (Konfirmasi Sukses)
            document.getElementById('step-2').style.display = 'none';
            document.getElementById('step-3').style.display = 'block';
        } else {
            alert('Gagal menyimpan pesanan: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan koneksi ke server.');
    });
}

// ================= SIMPAN BUKTI TRANSFER DI STEP 3 =================
function finishOrder() {
    const fileInput = document.getElementById('buktiBayar');
    
    // Validasi: pastikan user sudah memilih file bukti transfer
    if (!fileInput || !fileInput.files[0]) {
        alert('Silakan pilih dan upload file bukti transfer terlebih dahulu!');
        return;
    }

    const formData = new FormData();
    formData.append('order_id', lastCreatedOrderId);
    formData.append('buktiBayar', fileInput.files[0]);

    // Kirim file ke backend khusus upload bukti
    fetch('upload_bukti.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(`Pesanan berhasil disimpan beserta bukti transfer! ID Pesanan: ${lastCreatedOrderId}`);
            cart = [];
            updateCartBadge();
            document.getElementById('formCheckoutStep1')?.reset();
            document.getElementById('formCheckoutStep2')?.reset();
            
            // Reset input file dan teks nama file
            fileInput.value = '';
            const fileNameSpan = document.getElementById('namaFileBukti');
            if (fileNameSpan) {
                fileNameSpan.innerText = 'Belum ada file dipilih';
                fileNameSpan.style.color = '#64748b';
                fileNameSpan.style.fontWeight = 'normal';
            }

            goToStep1();
            closeCart();
            location.reload(); // Refresh halaman agar data admin langsung sinkron
        } else {
            alert('Gagal mengupload bukti: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan koneksi ke server.');
    });
}

// ================= MODAL CEK STATUS PESANAN (USER) =================
function openStatusModal() {
    window.location.hash = 'status-modal';
}

function closeStatusModal() {
    window.location.hash = '';
}

function searchOrderStatus() {
    const input = document.getElementById('statusPhoneInput')?.value.trim();
    const resultArea = document.getElementById('statusResultArea');

    if (!input) {
        alert('Masukkan Nomor HP atau ID Pesanan!');
        return;
    }

    if (!resultArea) return;

    resultArea.style.display = 'block';
    resultArea.innerHTML = `<p style="color:#6b7280; font-size:13px; text-align:center;"><i class="fa-solid fa-spinner fa-spin"></i> Memeriksa data pesanan...</p>`;

    fetch(`cek_status.php?keyword=${encodeURIComponent(input)}`)
        .then(response => response.json())
        .then(res => {
            if (res.status === 'success') {
                resultArea.innerHTML = res.data.map(order => {
                    let badgeStyle = 'background: #f3f4f6; color: #374151;';
                    if (order.status === 'Menunggu' || order.status === 'Pending') {
                        badgeStyle = 'background: #ffedd5; color: #c2410c;';
                    } else if (['Diproses', 'Proses', 'Sedang Dibuat'].includes(order.status)) {
                        badgeStyle = 'background: #dbeafe; color: #1d4ed8;';
                    } else if (order.status === 'Siap Dikirim') {
                        badgeStyle = 'background: #fef3c7; color: #b45309;';
                    } else if (order.status === 'Dalam Perjalanan') {
                        badgeStyle = 'background: #f3e8ff; color: #7e22ce;';
                    } else if (order.status === 'Selesai') {
                        badgeStyle = 'background: #dcfce7; color: #15803d;';
                    } else if (order.status === 'Dibatalkan') {
                        badgeStyle = 'background: #fee2e2; color: #b91c1c;';
                    }

                    return `
                        <div class="status-result-card" style="background:#f9fafb; padding:12px; border-radius:8px; margin-top:10px; border:1px solid #e5e7eb; text-align:left;">
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <strong>ID: ${order.id}</strong>
                                <span class="status-badge" style="padding:4px 8px; border-radius:4px; font-size:11px; font-weight:bold; ${badgeStyle}">${order.status}</span>
                            </div>
                            <div style="font-size:12px; color:#4b5563; margin-top:8px;">
                                <p>Pemesan: <b>${escapeHtml(order.nama)}</b></p>
                                <p>Tanggal PO: ${order.tanggal}</p>
                                <p>Detail: ${escapeHtml(order.detail)}</p>
                                <p style="margin-top:4px;">Total: <b style="color:#10b981;">${order.total}</b></p>
                            </div>
                        </div>
                    `;
                }).join('');
            } else {
                resultArea.innerHTML = `<p style="color:#ef4444; font-size:13px; text-align:center;">${res.message}</p>`;
            }
        })
        .catch(err => {
            console.error('Error:', err);
            resultArea.innerHTML = `<p style="color:#ef4444; font-size:13px; text-align:center;">Gagal terhubung ke server.</p>`;
        });
}

// ================= ADMIN DASHBOARD LOGIC =================
function loginAdmin() {
    const u = document.getElementById('adminUser')?.value.trim();
    const p = document.getElementById('adminPass')?.value.trim();

    if ((u === 'admin' || u === '') && (p === 'admin123' || p === '')) {
        if (document.getElementById('adminLoginForm')) document.getElementById('adminLoginForm').style.display = 'none';
        if (document.getElementById('adminDashboard')) document.getElementById('adminDashboard').style.display = 'block';
    } else {
        alert('Username atau Password Admin salah!');
    }
}

function logoutAdmin() {
    if (document.getElementById('adminLoginForm')) document.getElementById('adminLoginForm').style.display = 'block';
    if (document.getElementById('adminDashboard')) document.getElementById('adminDashboard').style.display = 'none';
}

function closeAdminModal() {
    window.location.hash = '';
}

function escapeHtml(text) {
    return String(text).replace(/[&<>"']/g, char => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    }[char]));
}

function toggleQrisView(radio) {
    const qrisBox = document.getElementById('qrisContainer');
    if (qrisBox) {
        if (radio.value === 'QRIS') {
            qrisBox.style.display = 'block';
        } else {
            qrisBox.style.display = 'none';
        }
    }
}