<table>
    <thead>
        <tr>
            <td>#</td>
            <td>Name</td>
        </tr>
    </thead>
	<tbody>
    <?php foreach ($data as $row) : ?>
		<tr>
			<td><?= $row['id'] ?></td>
			<td><?= $row['name'] ?></td>
		</tr>
    <?php endforeach; ?>
	</tbody>
</table>