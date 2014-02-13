<?php
/**
 * Security check - make sure we were included by the main plugin file
 */
if (!defined('REGISTRATION_LOAD_VIEW')) exit();
?>
<p>
	Velkommen til forhåndstilmeldingen til Djurslejren 2014.<br/>
	<?php if (empty($admin)) { ?>
		Du er logget på som: <strong><?= $user->get('display_name') ?></strong>. <?php wp_loginout('/'.REGISTRATION_PAGE_CONTROLLER_NAME); ?>
	<?php } else { ?>
		Du er logget på som: <strong><?= $admin->get('display_name') ?></strong>. <?php wp_loginout('/'.REGISTRATION_PAGE_CONTROLLER_NAME); ?><br/>
		Du ser i øjeblikket tilmeldinger for <strong><?= $user->get('display_name') ?></strong>. <a href="/<?= REGISTRATION_PAGE_CONTROLLER_NAME ?>/oversigt">Tilbage til oversigten</a>.
	<?php } ?>
</p>

<p>
	Forhåndtilmeldingen er lukket. Du kan dog herunder se dine tilmeldinger.<br/>
	Hvis du har væsentlige ændringer til din forhåndstilmelding, bedes du kontakte lejrledelsen på <a href="mailto:info@djurslejren.dk">info@djurslejren.dk</a>.
</p>

<h3>Betaling</h3>
<p>
	<?php
	foreach ($registrations as $i => $r)
	{
		$selectedAge = null;
		foreach ($ages as $a)
		{
			if ($r['age'] == $a['key'])
			{
				$total += $a['price'][$r['length']];
				$totals[0] += $a['rate'][$r['length']][0];
				$totals[1] += $a['rate'][$r['length']][1];
				$totals[2] += $a['rate'][$r['length']][2];
				continue 2;
			}
		}
	}
	?>
	Du skal senest den <b><?= strftime("%#d. %B %Y", strtotime($rates[0])) ?></b> indbetale <span class="rate"><?= number_format($totals[0], 0, ',', '.') ?></span> til konto <b><?= $account ?></b>.<br/>
	Du skal senest den <b><?= strftime("%#d. %B %Y", strtotime($rates[1])) ?></b> indbetale <span class="rate"><?= number_format($totals[1], 0, ',', '.') ?></span> til konto <b><?= $account ?></b>.<br/>
	Du skal senest den <b><?= strftime("%#d. %B %Y", strtotime($rates[2])) ?></b> indbetale <span class="rate"><?= number_format($totals[2], 0, ',', '.') ?></span> til konto <b><?= $account ?></b>.<br/>
	I alt skal du senest den <b><?= strftime("%#d. %B %Y", strtotime($rates[2])) ?></b> have indbetalt <span class="total"><?= number_format($total, 0, ',', '.') ?></span>.
</p>
<hr>
<h2>Deltagere</h2>
<form id="preregistration-update" data-userid="<?= $user->get('id') ?>">
	<fieldset>
		<p>For nemmere at kunne kontakte dig, skal vi bruge en e-mail adresse, indtast den venligst her:<br/>
			<label for="email">E-mail:</label> <input type="text" name="email" id="email" value="<?= $user->get('user_email') ?>" class="email" />
		</p>
		<span class="message"></span>
		<input type="submit" value="Gem e-mail" />
	</fieldset>
</form>

<table>
	<thead>
		<tr>
			<th>Navn</th>
			<th>Fødselsdato</th>
			<th>Særlige behov</th>
			<th>Aldersgruppe</th>
			<th>Længde</th>
			<th class="price">Lejrens pris</th>
			<th class="price">1. rate</th>
			<th class="price">2. rate</th>
			<th class="price">3. rate</th>
		</tr>
	</thead>
	<tbody>
		<?php
		if (!empty($registrations))
		{
			foreach ($registrations as $i => $r)
			{
			?>
				<tr data-id="<?= $i ?>">
					<td><?= $r['name'] ?></td>
					<td><?= date("d/m-Y", strtotime($r['birthdate'])) ?></td>
					<td><?= nl2br($r['needs']) ?></td>
					<td>
						<?php
							$selectedAge = null;
							foreach ($ages as $a)
							{
								if ($r['age'] == $a['key'])
								{
									$selectedAge = $a;
									echo $a['title'];
								}
							}
						?>
					</td>
					<td>
						<?= ($r['length'] == 'half') ? 'For de yngste' : 'Hele lejren' ?>
					</td>
					<td><?= number_format($selectedAge['price'][$r['length']], 0, ',', '.') ?></td>
					<td><?= number_format($selectedAge['rate'][$r['length']][0], 0, ',', '.') ?>
					<td><?= number_format($selectedAge['rate'][$r['length']][1], 0, ',', '.') ?>
					<td><?= number_format($selectedAge['rate'][$r['length']][2], 0, ',', '.') ?>
				</tr>
			<?php
			}
		}
		else
		{
		?>
		<tr>
			<td colspan="9">
				<em>Du har ingen forhåndstilmeldinger</em>
			</td>
		</tr>
		<?php
		}
		?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="5">Samlet betaling:</td>
			<td><?= number_format($total, 0, ',', '.') ?></td>
			<td><?= number_format($totals[0], 0, ',', '.') ?></td>
			<td><?= number_format($totals[1], 0, ',', '.') ?></td>
			<td><?= number_format($totals[2], 0, ',', '.') ?></td>
		</tr>
	</tfoot>
</table>