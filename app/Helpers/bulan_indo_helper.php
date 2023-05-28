<?php

function bulan_indonesia($date)
{
  $Bulan = array(
    "Januari", "Februari", "Maret", "April",
    "Mei", "Juni", "Juli", "Agustus", "September",
    "Oktober", "November", "Desember"
  );
  $tahun = substr($date, 0, 4);
  $bulan = substr($date, 5, 2);
  $tgl = substr($date, 8, 2);
  return $result = $Bulan[(int)$bulan - 1] . " " . $tahun;
}