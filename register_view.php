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

<h2>Vejledning</h2>
<p>
	For at udfylde forhåndstilmeldingen skal du blot udfylde de relevante felter herunder.<br/>
	Når du har udfyldt felterne, trykker du på knappen "Gem forhåndstilmelding".<br/>
</p>

<p>
	Du kan, indtil forhåndstilmeldingen lukker, altid komme tilbage og rette i oplysningerne.
</p>

<h3>Lejrens længde</h3>
<p>
	Når du udfylder fødselsdatoen på en spejder, vil den relevante aldersgruppe automatisk blive valgt.<br/>
	Denne er dog kun vejledende, og kan frit ændres efterfølgende. Du kan vælge imellem to længder på lejren.<br/>
	<ol>
		<li>Hele lejren</li>
		<li>For de yngste</li>
	</ol>
	<b>Hele lejren</b> betyder at spejderen deltager i hele lejrugen (19-26. juli).<br/>
	<b>For de yngste</b> betyder at spejderen deltager i den del af lejren som er målrettet de yngste deltagere (19-22. juli).<br/>
	De yngste deltagere (0-3. kl.) kan selvfølgelig også deltage på hele lejren, der vil dog ikke være et målrettet program i resten af lejrugen.
</p>

<h3>Betaling</h3>
<p>
	Du skal senest den <b><?= strftime("%#d. %B %Y", strtotime($rates[0])) ?></b> indbetale <span class="rate" data-rate="1"></span> til konto <b><?= $account ?></b>.<br/>
	Du skal senest den <b><?= strftime("%#d. %B %Y", strtotime($rates[1])) ?></b> indbetale <span class="rate" data-rate="2"></span> til konto <b><?= $account ?></b>.<br/>
	Du skal senest den <b><?= strftime("%#d. %B %Y", strtotime($rates[2])) ?></b> indbetale <span class="rate" data-rate="3"></span> til konto <b><?= $account ?></b>.<br/>
	I alt skal du senest den <b><?= strftime("%#d. %B %Y", strtotime($rates[2])) ?></b> have indbetalt <span class="total"></span>.
</p>

<form id="preregistration-update" data-userid="<?= $user->get('id') ?>">
	<fieldset>
		<p>For nemmere at kunne kontakte dig, skal vi bruge en e-mail adresse, indtast den venligst her:<br/>
			<label for="email">E-mail:</label> <input type="text" name="email" id="email" value="<?= $user->get('user_email') ?>" class="email" />
		</p>

		<table>
			<thead>
				<tr>
					<th>Fjern</th>
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
							<td><input type="button" class="remove" value="Fjern" /></td>
							<td><input type="text" name="name[]" value="<?= $r['name'] ?>" class="name" /></td>
							<td><input type="text" name="birthdate[]" value="<?= date("d/m-Y", strtotime($r['birthdate'])) ?>" class="birthdate" /></td>
							<td><textarea name="needs[]" class="needs"><?= $r['needs'] ?></textarea></td>
							<td>
								<select name="age[]" class="age">
									<?php
									$selectedAge = null;
									foreach ($ages as $a)
									{
										if ($r['age'] == $a['key'])
										{
											$s = 'selected="selected"';
											$selectedAge = $a;
										}
										else
										{
											$s = '';
										}
										?>
										<option value="<?= $a['key'] ?>" <?= $s ?>><?= $a['title'] ?></option>
										<?php
									}
									?>
								</select>
							</td>
							<td>
								<?php 
								if ($r['length'] == 'half')
								{
									$sH = 'selected="selected"';
									$sF = '';
								}
								else
								{
									$sF = 'selected="selected"';
									$sH = '';
								}
								?>
								<select name="length[]" class="length">
									<option value="full" <?= $sF ?>>Hele lejren</option>
									<option value="half" <?= $sH ?>>For de yngste</option>
								</select>
							</td>
							<td class="price" data-price="<?= $selectedAge['price'][$r['length']] ?>"><?= number_format($selectedAge['price'][$r['length']], 0, ',', '.') ?></td>
							<td class="rate" data-rate="1" data-price="<?= $selectedAge['rate'][$r['length']][0] ?>"><?= number_format($selectedAge['rate'][$r['length']][0], 0, ',', '.') ?>
							<td class="rate" data-rate="2" data-price="<?= $selectedAge['rate'][$r['length']][1] ?>"><?= number_format($selectedAge['rate'][$r['length']][1], 0, ',', '.') ?>
							<td class="rate" data-rate="3" data-price="<?= $selectedAge['rate'][$r['length']][2] ?>"><?= number_format($selectedAge['rate'][$r['length']][2], 0, ',', '.') ?>
						</tr>
					<?php
					}
				}
				?>
				<tr class="new">
					<td><input type="button" class="remove" value="Fjern" /></td>
					<td><input type="text" name="name[]" class="name" /></td>
					<td><input type="text" name="birthdate[]" class="birthdate" /></td>
					<td><textarea name="needs[]" class="needs"></textarea></td>
					<td>
						<select name="age[]" class="age">
							<option value="" selected="selected">Vælg aldersgruppe</option>
							<?php
							foreach ($ages as $a)
							{
								?>
								<option value="<?= $a['key'] ?>"><?= $a['title'] ?></option>
								<?php
							}
							?>
						</select>
					</td>
					<td>
						<select name="length[]" class="length">
							<option value="full" selected="selected">Hele lejren</option>
							<option value="half">For de yngste</option>
						</select>
					</td>
					<td class="price" data-price="0">0</td>
					<td class="rate" data-rate="1" data-price="0">0</td>
					<td class="rate" data-rate="2" data-price="0">0</td>
					<td class="rate" data-rate="3" data-price="0">0</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="6" class="totalhead">Samlet betaling:</td>
					<td class="total price"></td>
					<td class="total rate" data-rate="1"></td>
					<td class="total rate" data-rate="2"></td>
					<td class="total rate" data-rate="3"></td>
				</tr>
			</tfoot>
		</table>
		<span class="message"></span>
		<input type="submit" value="Gem forhåndstilmelding" />
	</fieldset>
</form>