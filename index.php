<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kantin Sehat</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        :root{
            --bg:#f5f7fb;
            --card:#ffffff;
            --accent:#2f8f72;
            --muted:#69707a;
        }
        *{box-sizing:border-box}
        body{font-family:Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; margin:0; background:var(--bg); color:#111}

        .navbar{background:linear-gradient(90deg,var(--accent),#4da98a); color:#fff; padding:20px 24px; text-align:center; box-shadow:0 2px 8px rgba(0,0,0,0.08)}
        .navbar p{margin:0; font-size:28px; font-weight:700; letter-spacing:0.2px}

        main.card-container{display:flex; gap:24px; padding:28px; max-width:1200px; margin:32px auto}

        /* each column */
        .container-menu, .container-kasir{background:var(--card); border-radius:12px; padding:20px; box-shadow:0 8px 30px rgba(26, 32, 36, 0.06); flex:1; min-width:260px}

        .header-top{display:flex; align-items:center; justify-content:space-between; margin-bottom:12px}
        .container-title{font-weight:700; font-size:18px; margin:0; color:#111}

        .button-container{display:flex; gap:12px; margin-top:16px}
        .btn-primary{background:var(--accent); color:white; border:0; padding:10px 14px; border-radius:8px; cursor:pointer}
        .btn-outline{background:transparent; border:1px solid rgba(0,0,0,0.08); padding:10px 14px; border-radius:8px; cursor:pointer}

        /* responsive: stack on small screens */
        @media (max-width:800px){
            main.card-container{flex-direction:column; padding:16px}
        }
    </style>
    
</head>

<body>

    <div class="navbar">
        <p>Kantin Sehat</p>
    </div>
   
    <main class="card-container" role="main" aria-label="Kantin Sehat main menu">

        <!-- SECTION MAKANAN -->
        <section class="container-menu" aria-labelledby="makanan-title">
            <div class="header-top">
                <p id="makanan-title" class="container-title">Makanan &amp; Minuman</p>
            </div>

            <div class="button-container">

                <!-- tombol utama -->
                <a href="menu/menu_index.php">
                    <button class="btn-primary">Kelola Menu</button>
                </a>

                <!-- tombol outline -->
                <a href="kategori/kategori_index.php">
                    <button class="btn-outline">Kelola Kategori</button>
                </a>

            </div>
        </section>

        <!-- SECTION MINUMAN -->
        <section class="container-kasir" aria-labelledby="kasir-title">
            <div class="header-top">
                <p id="kasir-title" class="container-title">Kasir</p>
            </div>

            <div class="button-container">
                <a href="transaksi/transaksi_index.php">
                    <button class="btn-primary">Transaksi Kasir</button>
                </a>

                <a href="transaksi/detail_list.php">
                    <button class="btn-outline">Detail Transaksi</button>
                </a>
            </div>
        </section>

    </main>
    
</body>
</html>
