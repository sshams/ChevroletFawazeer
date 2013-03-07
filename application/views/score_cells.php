<?php
$row_rsScoreboard = mysql_fetch_assoc($rsScoreboard);
$totalRows_rsScoreboard = mysql_num_rows($rsScoreboard);
$i = 1;
?>

<table border="0" cellpadding="0" cellspacing="0">
  <?php do { ?>
    <tr>
      <?php if ($totalRows_rsScoreboard > 0) { // Show if recordset not empty ?>
      	<td><?php ($i < 10) ? "0" : "" ?><?php $i ?></td>
        <td><?php echo $row_rsScoreboard['first']; ?> <?php echo $row_rsScoreboard['last']; ?></td>
        <td><?php echo $row_rsScoreboard['total']; ?></td>
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
