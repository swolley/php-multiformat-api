<table class="table">
    <thead>
        <tr>
            <td>#</td>
            <td>Name</td>
        </tr>
    </thead>
	<tbody>
    <?php foreach ($data['data'] as $row) : ?>
		<tr id="<?=$row[$data['rowId']]?>">
			<td><?= $row['id'] ?></td>
			<td><?= $row['name'] ?></td>
		</tr>
    <?php endforeach; ?>
	</tbody>
</table>