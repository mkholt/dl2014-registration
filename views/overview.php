<?php
/**
 * Security check - make sure we were included by the main plugin file
 */
if (!defined('REGISTRATION_LOAD_VIEW')) exit();
?>
<p>
	Velkommen til oversigten over tilmeldingen til Djurslejren 2014.<br/>
	Du er logget på som: <strong><?= $user->get('display_name') ?></strong>. <?php wp_loginout('/'.REGISTRATION_PAGE_CONTROLLER_NAME); ?>
</p>

<p>
	Herunder kan du se, hvilke tilmeldinger der er foretaget.<br/>
	Du kan rette i en tilmelding ved at klikke på det relevante felt.
</p>
<span class="message"></span>
<form id="registration-overview">
	<fieldset>
		<table>
			<thead>
				<tr>
					<th class="group">Gruppe</th>
					<?php foreach ($ages as $age) { ?>
						<th class="age" data-age="<?= $age['key'] ?>">
							<?= $age['title'] ?><br/>
							<?php if (!empty($age['age'][0])) { ?>
								(<?= $age['age'][0] ?>
								<?php if (!empty($age['age'][1])) { ?>
									- <?= $age['age'][1] ?> år
								<?php } else { ?>+ år <?php } ?>)
							<?php } ?>
						</th>
					<?php } ?>
                    <th>I alt</th>
                    <th>Afsluttet</th>
				</tr>
			</thead>
			<tbody>
		<?php
		$perAge = array();
		foreach ($ages as $a) {
			$perAge[$a['key']] = 0;
		}

		if (!empty($registrations))
		{
			foreach ($registrations as $uId => $group)
			{
			?>
				<tr class="groupRow <?= (empty($group['registrations'])) ? 'empty' : '' ?>"
					data-group="<?= $group['user']->get('ID') ?>"
					data-login="<?= $group['user']->get('user_login') ?>"
					data-name="<?= $group['user']->get('first_name') ?>"
					data-email="<?= $group['user']->get('user_email') ?>"
					data-organization="<?= strtolower(substr($group['user']->get('last_name'), 1, -1)) ?>"
					>
					<td class="group">
						<span class="name"><?= $group['user']->get('display_name') ?></span>
						<span class="editWrapper">
							<span>
								[ <a href="#" class="edit">Ret</a> ]
								[ <a href="#" class="delete">Slet</a> ]
								[ <a href="/<?= REGISTRATION_PAGE_CONTROLLER_NAME ?>/tilmeldinger/<?= $group['user']->get('id') ?>" class="registrations">Tilmeldinger</a> ]
							</span>
						</span>
					</td>
					<?php
						$p = $perAge;
						if (!empty($group['registrations']))
						{
							foreach ($group['registrations'] as $r)
							{
								$p[$r['age']]++;
							}
						}
						$total = 0;
						foreach ($ages as $a) { $total += $p[$a['key']]; ?>
							<td class="age" data-age="<?= $a['key'] ?>"><?= $p[$a['key']] ?></td>
						<?php }
					?>
					<td class="total"><?= $total ?></td>
                    <td><?= (!empty($group['final']) && $group['final']['final']) ? $group['final']['time']->format('d/m-Y H:i') : '-'?></td>
				</tr>
		<?php
			}
		}
		?>
				<tr class="groupRow new">
					<td class="group">
						<span class="name"></span>
						<span class="editWrapper">
							<span>
								[ <a href="#" class="edit">Ret</a> ]
								[ <a href="#" class="delete">Slet</a> ]
								[ <a href="#" class="registrations">Tilmeldinger</a> ]
							</span>
						</span>
					</td>
					<?php foreach ($ages as $a) { ?>
						<td class="age" data-age="<?= $a['key'] ?>">0</td>
					<?php } ?>
					<td class="total">0</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td>I alt</td>
					<?php
					foreach ($ages as $a)
					{
						?>
						<td class="totalDays" data-age="<?= $a['key'] ?>"></td>
						<?php
					}
					?>
					<td class="total"></td>
                    <td></td>
				</tr>
			</tfoot>
		</table>

		<div id="add-wrapper">
			<input type="button" value="Tilføj gruppe" class="add" />
		</div>
		<div id="hide-wrapper">
			<input type="button" value="Skjul grupper" class="hide" title="Skjul grupper uden tilmeldinger" />
		</div>
	</fieldset>
</form>
<span class="message"></span>

<div class="editDialogs">
	<div class="editDialog edit">
		<h1>Ret gruppe:</h1>
		<span class="message"></span>
		<form>
			<input type="hidden" class="id" />
			<label>Brugernavn:</label> <span class="user_login"></span><br/>
			<label for="name">Navn:</label>
				<input type="text" id="name" class="name" /><br/>
			<label for="email">E-mail:</label>
				<input type="text" id="email" class="email" /><br/>
			<label for="organization">Korps:</label>
				<select id="organization" class="organization">
					<?php
					foreach ($organizations as $k => $o)
					{
						?>
						<option value="<?= $k ?>"><?= $o ?></option>
						<?php
					}
					?>
				</select>
			<h2>Skift kodeord:</h2>
			<label for="password_edit">Nyt kodeord:</label>
				<input id="password_edit" type="password" class="pass" /><br/>
			<label for="repeat_password_edit">Gentag kodeord:</label>
				<input id="repeat_password_edit" type="password" class="repeatPass" /><br/>
			<label></label>
			<input type="submit" value="Gem" />
			<input type="button" class="cancel" value="Annuller" />
		</form>
	</div>

	<div class="editDialog delete">
		<h1>Slet gruppe</h1>
		<span class="message"></span>
		<form>
			<input type="hidden" class="id" />
			<p>
				Er du sikker på, at du vil fjerne gruppen <span class="name"></span>?
			</p>
			<br/>
			<input type="submit" value="Slet" />
			<input type="button" class="cancel" value="Annuller" />
		</form>
	</div>

	<div class="editDialog add">
		<h1>Tilføj gruppe</h1>
		<span class="message"></span>
		<form>
			<label for="name">Navn:</label>
				<input type="text" id="name" class="name" /><br/>
			<label for="email">E-mail:</label>
				<input type="text" id="email" class="email" /><br/>
			<label for="organization">Korps:</label>
				<select id="organization" class="organization">
					<?php
					foreach ($organizations as $k => $o)
					{
						?>
						<option value="<?= $k ?>"><?= $o ?></option>
						<?php
					}
					?>
				</select><br/>
			<label for="password_add">Kodeord:</label>
				<input id="password_add" type="password" class="pass" /><br/>
			<label for="repeat_password_add">Gentag kodeord:</label>
				<input id="repeat_password_add" type="password" class="repeatPass" /><br/>
			<label></label>
			<input type="submit" value="Tilføj" />
			<input type="button" class="cancel" value="Annuller" />
		</form>
	</div>
</div>