<?php
$message = "";
$config = array(
            "VODAFONE" => array(
                "1" => "GAZDUIRE",
                "2" => "GAZDUIRE",
				"3" => "GAZDUIRE",
				"4" => "GAZDUIRE",
				"5" => "GAZDUIRE",
				"6" => "GAZDUIRE",
				"7" => "GAZDUIRE",
				"8" => "GAZDUIRE",
				"9" => "GAZDUIRE",
				"10" => "GAZDUIRE",
                ),
            "ORANGE" => array(
                "1" => "GAZDUIRE",
                "2" => "GAZDUIRE",
				"3" => "GAZDUIRE",
				"4" => "GAZDUIRE",
				"5" => "GAZDUIRE",
				"6" => "GAZDUIRE",
				"7" => "GAZDUIRE",
				"8" => "GAZDUIRE",
				"9" => "GAZDUIRE",
				"10" => "GAZDUIRE",
				),
            "COSMOTE" => array(
                "1" => "GAZDUIRE",
                "2" => "GAZDUIRE",
				"3" => "GAZDUIRE",
				"4" => "GAZDUIRE",
				"5" => "GAZDUIRE",
				"6" => "GAZDUIRE",
				"7" => "GAZDUIRE",
				"8" => "GAZDUIRE",
				"9" => "GAZDUIRE",
				"10" => "GAZDUIRE",
                ));              
if (count($_POST) > 0)
{
    $company = $_POST['company'];
    $ammount = $_POST['ammount'];
	if ($_POST['company'] == COSMOTE) {
		if ($_POST['ammount'] == 1) { $tel = 1258;}
		elseif ($_POST['ammount'] == 2) { $tel = 1259; }
		elseif ($_POST['ammount'] == 3) { $tel = 7413; }
		elseif ($_POST['ammount'] == 4) { $tel = 7604; }
		elseif ($_POST['ammount'] == 5) { $tel = 7415; }
		elseif ($_POST['ammount'] == 6) { $tel = 7686; }
		elseif ($_POST['ammount'] == 7) { $tel = 7510; }
		elseif ($_POST['ammount'] == 8) { $tel = 7662; }
		elseif ($_POST['ammount'] == 9) { $tel = 7509; }
		elseif ($_POST['ammount'] == 10) { $tel = 7410; }
		else { echo _e('Ceva e gresit. Incearca din nou'); }
	}
	elseif ($_POST['company'] == VODAFONE) {
		if ($_POST['ammount'] == 1) { $tel = 1258;}
		elseif ($_POST['ammount'] == 2) { $tel = 1259; }
		elseif ($_POST['ammount'] == 3) { $tel = 7413; }
		elseif ($_POST['ammount'] == 4) { $tel = 7604; }
		elseif ($_POST['ammount'] == 5) { $tel = 7415; }
		elseif ($_POST['ammount'] == 6) { $tel = 7686; }
		elseif ($_POST['ammount'] == 7) { $tel = 7510; }
		elseif ($_POST['ammount'] == 8) { $tel = 7662; }
		elseif ($_POST['ammount'] == 9) { $tel = 7509; }
		elseif ($_POST['ammount'] == 10) { $tel = 7410; }
		else { echo _e('Ceva e gresit. Incearca din nou'); }
	}
	elseif ($_POST['company'] == ORANGE) {
		if ($_POST['ammount'] == 1) { $tel = 1258; }
		elseif ($_POST['ammount'] == 2) { $tel = 1259; }
		elseif ($_POST['ammount'] == 3) { $tel = 1337; }
		elseif ($_POST['ammount'] == 4) { $tel = 1378; }
		elseif ($_POST['ammount'] == 5) { $tel = 7515; }
		elseif ($_POST['ammount'] == 6) { $tel = 7516; }
		elseif ($_POST['ammount'] == 7) { $tel = 7517; }
		elseif ($_POST['ammount'] == 8) { $tel = 7518; }
		elseif ($_POST['ammount'] == 9) { $tel = 7519; }
		elseif ($_POST['ammount'] == 10) { $tel = 7520; }
		else { echo _e('Ceva e gresit. Incearca din nou'); }
	}
	else { echo "Ceva e gresit. Incearca din nou"; }
	echo $message = "<div style='background-color:#FFFFCC;border:0px solid black;padding:10px;'><p style='color: red; text-align: center'><strong>"; _e('Optiunile tale', 'sms'); echo $m1 = ":<br />OPERATOR: <span style='color: black; text-align: center'>$company </span><br /><span style='color: red; text-align: center'>"; _e('SUMA:', 'sms'); echo $m4 = "</span> <span style='color: black; text-align: center'>$ammount &#8364; <br /><br /></span><p style='color: red; text-align: center'>"; _e('Daca aceste informatii sunt corecte, te rugam sa trimiti un sms la nr.', 'sms'); echo $m2 = " <p style='color: black; text-align: center'><em>$tel</em></p> <p style='color: red; text-align: center'>"; _e('care sa contina urmatorul mesaj:', 'sms');echo $m3 = "</p> <p style='color: black; text-align: center'><em>{$config[$company][$ammount]}</strong></em></p></p>";
	echo '</div>';
}
?>
<form method="post" >
<select name="company">
    <option value="VODAFONE">Vodafone</option>
    <option value="ORANGE">Orange</option>
    <option value="COSMOTE">Cosmote</option>
</select>

<select name="ammount">
    <option value="1">1 &#8364;uro</option>
    <option value="2">2 &#8364;uro</option>
    <option value="3">3 &#8364;uro</option>
    <option value="4">4 &#8364;uro</option>
    <option value="5">5 &#8364;uro</option>
    <option value="6">6 &#8364;uro</option>
    <option value="7">7 &#8364;uro</option>
    <option value="8">8 &#8364;uro</option>
    <option value="9">9 &#8364;uro</option>
    <option value="10">10 &#8364;uro</option>
</select>
<br /><br />
<input type="submit" class="btn_orange" value="<?php _e('Obtine Cod', 'sms'); ?>"/>
</form>
<br /><br />