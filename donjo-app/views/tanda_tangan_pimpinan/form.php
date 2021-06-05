<div class="content-wrapper">
    <section class="content-header">
        <h1>Tambah Surat Untuk Tanda Tangan</h1>
        <ol class="breadcrumb">
            <li><a href="<?= site_url('hom_sid'); ?>"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="<?= site_url('surat'); ?>"> Layanan Surat</a></li>
            <li class="active">Tanda Tangan Elektronik</li>
        </ol>
    </section>
    <section class="content" id="maincontent">
        <div class="box box-info">
            <div class="box-header with-border">
                <a href="<?= site_url("tanda_tangan"); ?>"
                   class="btn btn-social btn-flat btn-info btn-sm btn-sm visible-xs-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block"
                   title="Kembali Ke Daftar Wilayah">
                    <i class="fa fa-arrow-circle-left "></i>Kembali ke Daftar Tanda Tangan Surat
                </a>
            </div>
            <form id="validasi" action="<?= $form_action; ?>" method="POST" enctype="multipart/form-data"
                  class="form-horizontal">
                <div class="box-body">
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="kode_surat">Pilih Surat</label>
                        <div class="col-sm-7">
                            <select class="form-control input-sm select2-tags required" id="idlog_surat"
                                    name="idlog_surat">
                                <option value="">-- Pilih NIK / Nama / Surat / Nomor --</option>
                                <?php foreach ($log_surat as $item): ?>
                                    <option value="<?= $item['id']; ?>" <?= selected($item['id'], $item["jenis_surat"]); ?>><?= $item['nik'] . ' / ' . $item['nama_pend'] . ' / ' . $item['jenis_surat'] . ' - ' . $item['no_surat']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="dokumen">Dokumen PDF</label>

                        <div class="col-sm-7">
                            <div class="input-group">
                                <input type="text" class="form-control" id="file_path"  name="dokumen" required>
                                <input type="file" class="hidden" id="file" name="dokumen">
                                <span class="input-group-btn">
											<button type="button" class="btn btn-info btn-flat" id="file_browser">
                                                <i class="fa fa-search"></i> Browse</button>
                            </span>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="box-footer">
                    <button type="reset" class="btn btn-social btn-flat btn-danger btn-sm"
                            onclick="reset_form($(this).val());"><i class="fa fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-social btn-flat btn-info btn-sm pull-right"><i
                                class="fa fa-check"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>
<script>
    $('document').ready(function () {
        syarat($('input[name=mandiri]:checked').val());
        $('input[name="mandiri"]').change(function () {
            syarat($(this).val());
        });
    });

    function syarat(tipe) {
        (tipe == '1' || tipe == null) ? $('#syarat').show() : $('#syarat').hide();
    }

    function reset_form() {
        $(".tipe").removeClass("active");
        $("input[name=mandiri").prop("checked", false);
        <?php if ($surat_master['mandiri'] == '1'): ?>
        $("#m1").addClass('active');
        $("#g1").prop("checked", true);
        <?php endif; ?>
        <?php if ($surat_master['mandiri'] != '1'): ?>
        $("#m2").addClass('active');
        $("#g2").prop("checked", true);
        <?php endif; ?>
        syarat($('input[name=mandiri]:checked').val());
    };

    function masaBerlaku() {
        var masa_berlaku = document.getElementById("masa_berlaku").value;
        if (masa_berlaku < 1) {
            document.getElementById("masa_berlaku").value = 1;
        } else if (masa_berlaku > 31) {
            document.getElementById("masa_berlaku").value = 31;
        }
    }
</script>
