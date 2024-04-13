<header class="d-flex flex-wrap">
    <nav class="navbar navbar-expand-md navbar-light bg-light col-12 p-0">
        <div class="container-fluid">
            <button class="thrift-shop navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav thrift-shop-nav-border">
                    <li class="nav-item" data-url="beranda.php">
                        <a class="nav-link" aria-current="page" href="/beranda.php">Beranda</a>
                    </li>
                    <li class="nav-item" data-url="profil.php">
                        <a class="nav-link" href="/profil.php">Profil</a>
                    </li>
                    <li class="nav-item" data-url="produk.php">
                        <a class="nav-link" href="/produk.php">Produk</a>
                    </li>
                    <li class="nav-item" data-url="cara_pembelian.php">
                        <a class="nav-link" href="/cara_pembelian.php">Cara Pembelian</a>
                    </li>
                    <?php
                    if ($userLogin != '') {
                        ?>
                        <li class="nav-item" data-url="keranjang.php">
                            <a class="nav-link" href="/keranjang.php">Keranjang</a>
                        </li>
                        <?php
                    }
                    ?>

                    <?php
                    if ($userLogin != '') {
                        ?>
                        <li class="nav-item" data-url="riwayat_transaksi.php">
                            <a class="nav-link" href="#">Riwayat Transaksi</a>
                        </li>
                        <?php
                    }
                    ?>
                    <li class="nav-item" data-url="akun.php">
                        <a class="nav-link" aria-current="page" href="/akun.php">
                            <?php
                            if ($userLogin != '') {
                                echo ('Akun');
                            } else {
                                echo ('Login');
                            }
                            ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="col-12 d-flex p-2 flex-wrap align-items-center">
        <div class="d-flex align-items-center col-12 col-md-6 justify-content-md-start justify-content-center">
            <img src="/static/image/icons8-home-24.png" alt="home">
            <!-- will look on url using javascript -->
            <p class="m-0">&nbsp;Anda berada di:&nbsp;<span id="pagePosition" class="thrift-shop-font-orange"></span>
            </p>
        </div>
        <form id="searchBar" action="/produk.php" class="d-flex col-12 col-md-6 justify-content-md-end">
            <input id="cariItem" name="cari" type="text" placeholder="cari produk"
                class="thrift-shop-border-inside thrit-shop-width-search">
            <button class="thrift-shop-transparent thrift-shop-bg-dark"><img src="/static/image/icons8-search-24.png"
                    alt="search"></button>
        </form>
    </div>
</header>