<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class P001_hkejuruan extends Command
{
    protected $signature = 'sp:hkejuruan';
    protected $description = 'SP 001 hkejuruan';

    private $spname = "hkejuruan";

    public function handle()
    {
        try {
            $data = DB::unprepared("
DROP PROCEDURE IF EXISTS `{$this->spname}`;

CREATE DEFINER=`root`@`localhost` PROCEDURE `$this->spname`()
BEGIN
  DECLARE done INT DEFAULT FALSE;
  DECLARE dkd_jurusan   VARCHAR(10);
  DECLARE dnama_jurusan VARCHAR(25);
  DECLARE dtincome CURSOR FOR SELECT * FROM tempcat;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  DROP TEMPORARY TABLE IF EXISTS temp_jurusan;
  CREATE TEMPORARY TABLE temp_jurusan(
    kd_jurusan   VARCHAR(10),
    nama_jurusan VARCHAR(25)
  );

  INSERT INTO temp_jurusan (kd_jurusan, nama_jurusan)
  SELECT kd_jurusan, nama_jurusan
    FROM jurusan;

  SET @cekdt = (SELECT COUNT(*) FROM temp_jurusan);
  IF @cekdt > 0 THEN
     OPEN dtincome;
	   read_loop: LOOP
       FETCH dtincome into dkd_jurusan, dnama_jurusan;
       IF done THEN
          LEAVE read_loop;
       END IF;
     END LOOP;
     CLOSE dtincome;
  END IF;
END;");
            $this->info("Success Create Or Update SP {$this->spname} ({$data})");
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }
    }
}
