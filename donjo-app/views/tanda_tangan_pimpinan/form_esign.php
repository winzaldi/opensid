<script type="text/javascript" src="<?= base_url() ?>assets/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?= base_url() ?>assets/js/validasi.js"></script>
<script type="text/javascript" src="<?= base_url()?>assets/js/localization/messages_id.js"></script>
<script type="text/javascript" src="<?= base_url() ?>assets/js/script.js"></script>
<style type="text/css">
	.horizontal {
		padding-left: 0px;
		width: auto;
		padding-right: 30px;
	}
</style>
<form action="<?= $form_action ?>" method="post"  class="form-horizontal">
	<div class='modal-body'>
		<div class="row">
			<div class="col-sm-12">
				<div class="box box-danger">
					<div class="box-body">

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="nik">NIK</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control input-sm required" placeholder="nik" name="nik"></input>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="passprhase">Passprhase(PIN)</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control input-sm required" placeholder="Passprhase" name="passprhase"></text>
                                <input name="id" type="hidden" value="<?= $data['id']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="halaman">Halaman</label>
                            <div class="col-sm-7">
                                <input type="number" value="1" class="form-control input-sm required" placeholder="halaman" name="halaman"></input>
                            </div>
                        </div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="reset" class="btn btn-social btn-flat btn-danger btn-sm" data-dismiss="modal"><i class='fa fa-sign-out'></i> Tutup</button>
			<button type="submit" class="btn btn-social btn-flat btn-info btn-sm" id="ok"><i class='fa fa-check'></i> Simpan</button>
		</div>
	</div>
</form>