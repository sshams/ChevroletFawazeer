<?php
$row_rsScoreboard = mysql_fetch_assoc($rsScoreboard);
$totalRows_rsScoreboard = mysql_num_rows($rsScoreboard);
$i = 1;
?>

<table width="454" border="0" cellspacing="10" cellpadding="0">
  <col width="25" height="10" />
  <col width="230" />
  <col width="35" align="right" />
  <?php do { ?>
    <tr>
      <?php if ($totalRows_rsScoreboard > 0) { // Show if recordset not empty ?>
        <td width="25"><?php ($i < 10) ? "0" . $i : $i ?></td>
        <td width="230"><?php echo $row_rsScoreboard['first']; ?> <?php echo $row_rsScoreboard['last']; ?></td>
        <td width="35"><?php echo $row_rsScoreboard['total']; ?></td>
        <?php } // Show if recordset not empty ?>
    </tr>
    <?php } while ($row_rsScoreboard = mysql_fetch_assoc($rsScoreboard)); ?>
</table>
<?php if ($totalRows_rsScoreboard == 0) { // Show if recordset empty ?>
  <p>coming up!</p>
  <?php } // Show if recordset empty ?>
<?php
mysql_free_result($rsScoreboard);
?>
