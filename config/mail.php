<?php
// config/mail.php - William Lawson's "The Next Highlandah" Email Service
// Branded typography: Bebas Neue, Syne, Outfit, JetBrains Mono
// Official brand logo: images/logo.svg embedded via CID with fallback
// No emojis in email body contents (emojis retained in subjects only)

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

/**
 * Returns a preconfigured PHPMailer instance for SMTP.
 *
 * @param bool $debug
 * @return PHPMailer
 */
function createMailerInstance($debug = false) {
    $mail = new PHPMailer(true);

    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'mail.bayeapp.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'noreply@bayeapp.com';
    $mail->Password   = '@Niyilemo35';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->Timeout    = 10;

    // SSL options for hosting environments
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true
        ]
    ];

    if ($debug) {
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    } else {
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
    }

    $mail->setFrom('noreply@bayeapp.com', "William Lawson's Nigeria");
    $mail->addReplyTo('noreply@bayeapp.com', "William Lawson's Nigeria");
    $mail->CharSet = 'UTF-8';

    return $mail;
}

/**
 * Attaches the official William Lawson's logo.svg to a PHPMailer instance.
 *
 * @param PHPMailer $mailerInstance
 */
function attachBrandLogo(&$mailerInstance) {
    $svgPath = __DIR__ . '/../images/logo.svg';
    if (file_exists($svgPath)) {
        try {
            $mailerInstance->addEmbeddedImage($svgPath, 'wl_brand_logo', 'logo.svg', 'base64', 'image/svg+xml');
        } catch (Exception $e) {
            error_log("Failed to embed logo.svg: " . $e->getMessage());
        }
    }
}

/**
 * Standardized William Lawson's email typography head block with Google Fonts.
 *
 * @return string
 */
function getEmailFontStylesHtml() {
    return '
    <meta name="color-scheme" content="dark">
    <meta name="supported-color-schemes" content="dark">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=JetBrains+Mono:wght@700;800&family=Outfit:wght@400;500;600;700;800&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <!--[if !mso]><!-->
    <style>
      :root {
        color-scheme: dark;
        supported-color-schemes: dark;
      }
      @import url(\'https://fonts.googleapis.com/css2?family=Bebas+Neue&family=JetBrains+Mono:wght@700;800&family=Outfit:wght@400;500;600;700;800&family=Syne:wght@700;800&display=swap\');
      
      body, table, td, p, a, li, span {
        font-family: \'Outfit\', -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif !important;
      }
      .font-display {
        font-family: \'Bebas Neue\', \'Arial Black\', Impact, sans-serif !important;
        letter-spacing: 1.5px !important;
      }
      .font-heading {
        font-family: \'Syne\', Arial, Helvetica, sans-serif !important;
        font-weight: 800 !important;
      }
      .font-body {
        font-family: \'Outfit\', -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif !important;
      }
      .font-mono {
        font-family: \'JetBrains Mono\', \'Courier New\', Courier, monospace !important;
        letter-spacing: 3px !important;
      }
    </style>
    <!--<![endif]-->
    ';
}

/**
 * Generates the standardized William Lawson's email footer.
 * Logo centered flanked by green lines on both left and right sides.
 * No emojis in footer text.
 *
 * @return string
 */
function getEmailBrandFooterHtml() {
    $siteUrl = 'https://tnhng.com';
    return '
    <!-- FOOTER SECTION -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 30px; background-color: #050d08; border-top: 2px solid #004d13;">
      <tr>
        <td style="padding: 26px 20px 22px 20px; text-align: center;">

          <!-- Centered Logo Flanked by Horizontal Lines on Both Left & Right -->
          <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 0 auto 16px auto; max-width: 500px;">
            <tr>
              <td style="width: 36%; border-bottom: 2px solid #00c853; font-size: 0; line-height: 0;">&nbsp;</td>
              <td align="center" style="width: 28%; padding: 0 14px; white-space: nowrap;">
                <img src="cid:wl_brand_logo" alt="William Lawson\'s" width="95" height="58" style="display: block; margin: 0 auto; max-width: 95px; height: auto; border: 0;" />
              </td>
              <td style="width: 36%; border-bottom: 2px solid #00c853; font-size: 0; line-height: 0;">&nbsp;</td>
            </tr>
          </table>

          <p style="margin: 0 0 8px 0; font-family: \'Bebas Neue\', \'Syne\', \'Arial Black\', Impact, sans-serif; font-size: 16px; letter-spacing: 2px; color: #00c853; text-transform: uppercase;">
            NO RULES. GREAT SCOTCH.
          </p>

          <p style="margin: 0 0 14px 0; font-family: \'Outfit\', Arial, sans-serif; font-size: 11px; line-height: 16px; color: #9ca3af;">
            The Next Highlandah is an official national activation by William Lawson\'s Blended Scotch Whisky.<br />
            Track live standings and tour dates at <a href="' . $siteUrl . '" style="color: #00c853; text-decoration: underline; font-weight: 600;">tnhng.com</a>.
          </p>

          <!-- 18+ Drink Responsibly Tag -->
          <div style="display: inline-block; padding: 4px 12px; border: 1px solid #1f2937; border-radius: 4px; font-family: \'Outfit\', Arial, sans-serif; font-size: 10px; color: #6b7280; letter-spacing: 1px; text-transform: uppercase;">
            Strictly 18+ &bull; Drink Responsibly &bull; &copy; ' . date('Y') . ' William Lawson\'s
          </div>

        </td>
      </tr>
    </table>
    ';
}

/**
 * Sends a welcome email to a newly registered Clan participant.
 *
 * @param array $participant Associative array with full_name, email, clan_id, phone, etc.
 * @return bool
 */
function sendClanWelcomeEmail($participant) {
    if (empty($participant['email']) || !filter_var($participant['email'], FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $fullName = htmlspecialchars($participant['full_name'], ENT_QUOTES, 'UTF-8');
    $clanId   = htmlspecialchars($participant['clan_id'], ENT_QUOTES, 'UTF-8');
    $phone    = htmlspecialchars($participant['phone'] ?? '', ENT_QUOTES, 'UTF-8');

    $subject  = '🔥 Welcome to The Clan! Your Highlandah Pass is Ready [' . $participant['clan_id'] . ']';

    $fontStylesHtml = getEmailFontStylesHtml();
    $footerHtml     = getEmailBrandFooterHtml();

    $htmlBody = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome to The Clan</title>
  {$fontStylesHtml}
</head>
<body style="margin: 0; padding: 0; font-family: 'Outfit', -apple-system, BlinkMacSystemFont, Arial, sans-serif; -webkit-font-smoothing: antialiased;">
  <!-- CONTAINER TABLE -->
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 0 auto; padding: 10px 0;">
    <tr>
      <td align="center">
        <!-- EMAIL CARD -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#0d120f" style="max-width: 680px; background-color: #0d120f; border: 1px solid #004d13; border-radius: 8px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.85);">
          
          <!-- BRAND BANNER HEADER -->
          <tr>
            <td bgcolor="#003822" style="background-color: #003822; padding: 32px 24px 24px 24px; text-align: center; border-bottom: 3px solid #00c853;">
              
              <!-- Brand Logo in Header -->
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 16px;">
                <tr>
                  <td align="center">
                    <img src="cid:wl_brand_logo" alt="William Lawson's" width="120" height="73" style="display: block; margin: 0 auto; max-width: 120px; height: auto; border: 0;" />
                  </td>
                </tr>
              </table>

              <span style="display: inline-block; background-color: #00c853; color: #000000; font-family: 'Syne', 'Outfit', Arial, sans-serif; font-weight: 800; font-size: 11px; padding: 4px 14px; border-radius: 20px; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 10px;">
                Official Highlandah Pass
              </span>
              <h1 class="font-display" style="margin: 0 0 6px 0; color: #ffffff; font-family: 'Bebas Neue', 'Syne', 'Arial Black', Impact, sans-serif; font-size: 30px; line-height: 34px; text-transform: uppercase; letter-spacing: 2px;">
                WELCOME TO THE CLAN
              </h1>
              <p style="margin: 0; color: #a7f3d0; font-family: 'Outfit', Arial, sans-serif; font-size: 14px; letter-spacing: 0.5px;">
                You are now officially enrolled in <strong>The Next Highlandah</strong>.
              </p>
            </td>
          </tr>

          <!-- MAIN CONTENT BODY -->
          <tr>
            <td style="padding: 28px 24px 10px 24px;">
              <p style="margin: 0 0 18px 0; color: #d1d5db; font-family: 'Outfit', Arial, sans-serif; font-size: 15px; line-height: 22px;">
                Salute, <strong style="color: #ffffff;">{$fullName}</strong>. You have taken the oath. Your unique Clan Pass is now active and ready to use at any participating tour bar, club, or lounge across Nigeria.
              </p>

              <!-- HIGHLANDAH PASS CARD -->
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #060907; border: 2px dashed #00c853; border-radius: 6px; margin: 20px 0; padding: 18px;">
                <tr>
                  <td align="center" style="padding: 12px 8px;">
                    <div style="font-family: 'Syne', Arial, sans-serif; font-size: 11px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 6px;">
                      Your Unique Clan Pass ID
                    </div>
                    <div class="font-mono" style="font-family: 'JetBrains Mono', 'Courier New', Courier, monospace; font-size: 32px; font-weight: 800; color: #facc15; letter-spacing: 4px; background-color: #000000; display: inline-block; padding: 8px 24px; border-radius: 4px; border: 1px solid #facc15;">
                      {$clanId}
                    </div>
                    <div style="font-family: 'Outfit', Arial, sans-serif; font-size: 12px; color: #00c853; margin-top: 10px; font-weight: 500;">
                      Phone: {$phone} &bull; Opening Balance: <strong>0 PTS</strong>
                    </div>
                  </td>
                </tr>
              </table>

              <!-- GRAND FINALE TARGET BOX -->
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #111714; border-left: 4px solid #facc15; border-radius: 4px; margin-bottom: 24px;">
                <tr>
                  <td style="padding: 14px 18px;">
                    <div class="font-heading" style="color: #facc15; font-family: 'Syne', Arial, sans-serif; font-weight: 800; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">
                      THE 100-POINT TARGET
                    </div>
                    <div style="color: #d1d5db; font-family: 'Outfit', Arial, sans-serif; font-size: 13px; line-height: 19px;">
                      Accumulate <strong>100 points</strong> across any of our activation outlets to qualify for the Grand Finale draw and earn an exclusive VIP celebrity table experience.
                    </div>
                  </td>
                </tr>
              </table>

              <!-- HOW IT WORKS (3 STEPS) -->
              <h2 class="font-display" style="margin: 24px 0 14px 0; color: #ffffff; font-size: 20px; font-family: 'Bebas Neue', 'Syne', Arial, sans-serif; text-transform: uppercase; letter-spacing: 1.5px;">
                HOW TO BANK POINTS
              </h2>

              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 24px;">
                <!-- Step 1 -->
                <tr>
                  <td width="36" valign="top" style="padding-bottom: 14px;">
                    <div style="width: 28px; height: 28px; background-color: #00c853; color: #000000; font-family: 'Syne', Arial, sans-serif; font-weight: 800; border-radius: 50%; text-align: center; line-height: 28px; font-size: 13px;">1</div>
                  </td>
                  <td valign="top" style="padding-bottom: 14px; padding-left: 10px;">
                    <strong style="color: #ffffff; font-family: 'Syne', Arial, sans-serif; font-size: 14px;">Visit Participating Outlets</strong>
                    <p style="margin: 4px 0 0 0; color: #9ca3af; font-family: 'Outfit', Arial, sans-serif; font-size: 13px; line-height: 18px;">
                      Walk into any William Lawson\'s partner outlet during tour dates nationwide.
                    </p>
                  </td>
                </tr>
                <!-- Step 2 -->
                <tr>
                  <td width="36" valign="top" style="padding-bottom: 14px;">
                    <div style="width: 28px; height: 28px; background-color: #00c853; color: #000000; font-family: 'Syne', Arial, sans-serif; font-weight: 800; border-radius: 50%; text-align: center; line-height: 28px; font-size: 13px;">2</div>
                  </td>
                  <td valign="top" style="padding-bottom: 14px; padding-left: 10px;">
                    <strong style="color: #ffffff; font-family: 'Syne', Arial, sans-serif; font-size: 14px;">Call Out Your Clan ID</strong>
                    <p style="margin: 4px 0 0 0; color: #9ca3af; font-family: 'Outfit', Arial, sans-serif; font-size: 13px; line-height: 18px;">
                      Show this email or tell the on-site Brand Ambassador your Clan ID: <code style="color: #facc15; font-family: 'JetBrains Mono', monospace; font-weight: 700;">{$clanId}</code>. No duplicate registration required.
                    </p>
                  </td>
                </tr>
                <!-- Step 3 -->
                <tr>
                  <td width="36" valign="top" style="padding-bottom: 14px;">
                    <div style="width: 28px; height: 28px; background-color: #00c853; color: #000000; font-family: 'Syne', Arial, sans-serif; font-weight: 800; border-radius: 50%; text-align: center; line-height: 28px; font-size: 13px;">3</div>
                  </td>
                  <td valign="top" style="padding-bottom: 14px; padding-left: 10px;">
                    <strong style="color: #ffffff; font-family: 'Syne', Arial, sans-serif; font-size: 14px;">Spin the Bottle & Conquer Challenges</strong>
                    <p style="margin: 4px 0 0 0; color: #9ca3af; font-family: 'Outfit', Arial, sans-serif; font-size: 13px; line-height: 18px;">
                      Complete official bold challenges to earn instant verified scorecard points synced immediately to the national leaderboard.
                    </p>
                  </td>
                </tr>
              </table>

              <!-- CTA BUTTONS -->
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 10px 0 20px 0;">
                <tr>
                  <td align="center">
                    <a href="https://tnhng.com/leaderboard" class="font-display" style="display: inline-block; background-color: #00c853; color: #000000; font-family: 'Bebas Neue', 'Syne', 'Arial Black', sans-serif; font-size: 16px; font-weight: 700; text-decoration: none; padding: 12px 28px; border-radius: 4px; text-transform: uppercase; letter-spacing: 1.5px; margin: 6px;">
                      View Live Leaderboard
                    </a>
                    <a href="https://tnhng.com/outlets" class="font-display" style="display: inline-block; background-color: transparent; border: 2px solid #00c853; color: #00c853; font-family: 'Bebas Neue', 'Syne', 'Arial Black', sans-serif; font-size: 16px; font-weight: 700; text-decoration: none; padding: 10px 24px; border-radius: 4px; text-transform: uppercase; letter-spacing: 1.5px; margin: 6px;">
                      Find Outlets to Play
                    </a>
                  </td>
                </tr>
              </table>

            </td>
          </tr>

          <!-- BRAND FOOTER -->
          <tr>
            <td>
              {$footerHtml}
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;

    $altBody = "Welcome to The Clan, {$participant['full_name']}!\n\nYour Clan Pass ID: {$participant['clan_id']}\nPhone: {$participant['phone']}\nOpening Balance: 0 PTS\nTarget: 100 PTS to Grand Finale Table Draw\n\nVisit any William Lawson's tour outlet to play and bank points!\nLive standings: https://tnhng.com/leaderboard\n\nNO RULES. GREAT SCOTCH.\nStrictly 18+ Drink Responsibly";

    // Primary delivery attempt via SMTP
    try {
        $mail = createMailerInstance();
        $mail->addAddress($participant['email'], $participant['full_name']);
        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body    = $htmlBody;
        $mail->AltBody = $altBody;
        attachBrandLogo($mail);

        $mail->send();
        return true;
    } catch (Exception $smtpEx) {
        error_log("PHPMailer SMTP failed ({$mail->ErrorInfo}), attempting native mail() fallback...");
        try {
            $fallback = new PHPMailer(true);
            $fallback->isMail();
            $fallback->setFrom('noreply@bayeapp.com', "William Lawson's Nigeria");
            $fallback->addReplyTo('noreply@bayeapp.com', "William Lawson's Nigeria");
            $fallback->addAddress($participant['email'], $participant['full_name']);
            $fallback->Subject = $subject;
            $fallback->isHTML(true);
            $fallback->Body    = $htmlBody;
            $fallback->AltBody = $altBody;
            $fallback->CharSet = 'UTF-8';
            attachBrandLogo($fallback);

            $fallback->send();
            return true;
        } catch (Exception $e2) {
            error_log("Both SMTP and native mail() failed for {$participant['email']}: " . $e2->getMessage());
            return false;
        }
    }
}

/**
 * Sends a notification email each time points are accumulated.
 *
 * @param array $participant Associative array with full_name, email, clan_id, total_points, etc.
 * @param array $allocation Associative array with points, challenge, category, outlet_name, promoter_name, etc.
 * @return bool
 */
function sendClanPointsEmail($participant, $allocation) {
    if (empty($participant['email']) || !filter_var($participant['email'], FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $awardedPoints = intval($allocation['points'] ?? 0);
    $outletName    = htmlspecialchars($allocation['outlet_name'] ?? 'Partner Outlet', ENT_QUOTES, 'UTF-8');
    $challengeName = htmlspecialchars($allocation['challenge'] ?? 'Highlandah Challenge', ENT_QUOTES, 'UTF-8');
    $category      = htmlspecialchars($allocation['category'] ?? 'General', ENT_QUOTES, 'UTF-8');
    $promoterName  = htmlspecialchars($allocation['promoter_name'] ?? 'Brand Ambassador', ENT_QUOTES, 'UTF-8');

    $fullName      = htmlspecialchars($participant['full_name'], ENT_QUOTES, 'UTF-8');
    $clanId        = htmlspecialchars($participant['clan_id'], ENT_QUOTES, 'UTF-8');
    $totalPoints   = intval($participant['total_points'] ?? 0);

    // Subject retains emoji per instructions
    $subject       = "⚡ +{$awardedPoints} PTS Banked at {$outletName}! [{$clanId}]";

    $progressPct  = min(100, max(0, round(($totalPoints / 100) * 100)));
    $pointsNeeded = max(0, 100 - $totalPoints);
    $isQualified  = ($totalPoints >= 100);

    $fontStylesHtml = getEmailFontStylesHtml();
    $footerHtml     = getEmailBrandFooterHtml();

    // Milestone notice (strictly no emojis in email body)
    if ($isQualified) {
        $milestoneHtml = '
        <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#064e3b" style="background-color: #064e3b; border: 2px solid #00c853; border-radius: 6px; margin: 20px 0;">
          <tr>
            <td style="padding: 18px; text-align: center;">
              <div class="font-display" style="font-size: 22px; font-weight: bold; color: #facc15; font-family: \'Bebas Neue\', \'Syne\', Arial, sans-serif; text-transform: uppercase; letter-spacing: 2px;">
                GRAND FINALE QUALIFIED
              </div>
              <p style="margin: 6px 0 0 0; color: #ecfdf5; font-family: \'Outfit\', Arial, sans-serif; font-size: 13px; line-height: 18px;">
                You have officially reached the <strong>100 PTS milestone</strong>. Your entry has been locked in for the celebrity VIP Table Grand Finale draw!
              </p>
            </td>
          </tr>
        </table>';
    } else {
        $milestoneHtml = '
        <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#111714" style="background-color: #111714; border-left: 4px solid #facc15; border-radius: 4px; margin: 20px 0;">
          <tr>
            <td style="padding: 14px 18px;">
              <div class="font-heading" style="color: #facc15; font-family: \'Syne\', Arial, sans-serif; font-weight: 800; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px;">
                TARGET REMAINING:
              </div>
              <div style="color: #cbd5e1; font-family: \'Outfit\', Arial, sans-serif; font-size: 13px;">
                Only <strong>' . $pointsNeeded . ' more points</strong> needed to unlock the 100 PTS Grand Finale VIP Table Draw.
              </div>
            </td>
          </tr>
        </table>';
    }

    $htmlBody = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Points Banked Notification</title>
  {$fontStylesHtml}
</head>
<body style="margin: 0; padding: 0; font-family: 'Outfit', -apple-system, BlinkMacSystemFont, Arial, sans-serif; -webkit-font-smoothing: antialiased;">
  <!-- CONTAINER TABLE -->
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 0 auto; padding: 10px 0;">
    <tr>
      <td align="center">
        <!-- EMAIL CARD -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#0d120f" style="max-width: 680px; background-color: #0d120f; border: 1px solid #004d13; border-radius: 8px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.85);">
          
          <!-- BANNER HEADER -->
          <tr>
            <td bgcolor="#003822" style="background-color: #003822; padding: 32px 24px 22px 24px; text-align: center; border-bottom: 3px solid #00c853;">
              
              <!-- Brand Logo in Header -->
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 16px;">
                <tr>
                  <td align="center">
                    <img src="cid:wl_brand_logo" alt="William Lawson's" width="120" height="73" style="display: block; margin: 0 auto; max-width: 120px; height: auto; border: 0;" />
                  </td>
                </tr>
              </table>

              <span style="display: inline-block; background-color: #facc15; color: #000000; font-family: 'Syne', Arial, sans-serif; font-weight: 800; font-size: 11px; padding: 4px 14px; border-radius: 20px; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 10px;">
                Live Scorecard Sync
              </span>
              <h1 class="font-display" style="margin: 0 0 6px 0; color: #ffffff; font-family: 'Bebas Neue', 'Syne', 'Arial Black', Impact, sans-serif; font-size: 32px; line-height: 36px; text-transform: uppercase; letter-spacing: 2px;">
                POINTS BANKED
              </h1>
              <p style="margin: 0; color: #a7f3d0; font-family: 'Outfit', Arial, sans-serif; font-size: 14px;">
                Scorecard successfully verified at <strong>{$outletName}</strong>
              </p>
            </td>
          </tr>

          <!-- MAIN BODY -->
          <tr>
            <td style="padding: 26px 24px 10px 24px;">
              <p style="margin: 0 0 18px 0; color: #d1d5db; font-family: 'Outfit', Arial, sans-serif; font-size: 15px; line-height: 22px;">
                Highlandah <strong style="color: #ffffff;">{$fullName}</strong> (<code style="font-family: 'JetBrains Mono', monospace; color: #facc15; font-weight: 700;">{$clanId}</code>), your challenge results were verified and locked in by Brand Ambassador <strong>{$promoterName}</strong>.
              </p>

              <!-- SCORECARD SUMMARY CARD (Shielded for both Light Mode & Dark Mode) -->
              <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#060907" style="background-color: #060907; border: 1px solid #004d13; border-radius: 8px; margin-bottom: 22px; overflow: hidden;">
                <tr>
                  <td bgcolor="#060907" style="padding: 22px 20px; text-align: center; border-bottom: 1px solid #14241b; background-color: #060907;">
                    <div style="font-family: 'Syne', Arial, sans-serif; font-size: 11px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 4px;">
                      Points Awarded
                    </div>
                    <div class="font-display" style="font-family: 'Bebas Neue', 'Arial Black', Impact, sans-serif; font-size: 44px; color: #00c853; line-height: 48px; letter-spacing: 2px;">
                      +{$awardedPoints} <span style="font-size: 20px; color: #facc15;">PTS</span>
                    </div>
                    <div style="font-family: 'Outfit', Arial, sans-serif; font-size: 13px; color: #e5e7eb; margin-top: 4px;">
                      Challenge: <strong>{$challengeName}</strong> ({$category})
                    </div>
                  </td>
                </tr>
                <tr>
                  <td bgcolor="#0a130e" style="padding: 16px 20px; background-color: #0a130e;">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#0a130e" style="background-color: #0a130e;">
                      <tr>
                        <td valign="middle" style="color: #cbd5e1; font-family: 'Outfit', Arial, sans-serif; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                          Activation Outlet:
                        </td>
                        <td align="right" valign="middle">
                          <table cellpadding="0" cellspacing="0" border="0" bgcolor="#032011" style="background-color: #032011; border: 1px solid #00c853; border-radius: 4px; display: inline-table;">
                            <tr>
                              <td bgcolor="#032011" style="padding: 5px 12px; color: #00c853; font-family: 'Outfit', Arial, sans-serif; font-size: 13px; font-weight: 700; text-align: right; background-color: #032011;">
                                {$outletName}
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                      <tr>
                        <td valign="middle" style="color: #cbd5e1; font-family: 'Outfit', Arial, sans-serif; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding-top: 12px;">
                          Total Points Now:
                        </td>
                        <td align="right" valign="middle" style="padding-top: 12px;">
                          <table cellpadding="0" cellspacing="0" border="0" bgcolor="#003822" style="background-color: #003822; border: 1px solid #00c853; border-radius: 4px; display: inline-table;">
                            <tr>
                              <td bgcolor="#003822" style="padding: 4px 14px; color: #facc15; font-family: 'Bebas Neue', 'Syne', Arial, sans-serif; font-size: 20px; font-weight: 800; letter-spacing: 1.5px; text-align: right; background-color: #003822;">
                                {$totalPoints} PTS
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              <!-- PROGRESS BAR -->
              <div style="margin: 22px 0 10px 0;">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                  <tr>
                    <td class="font-heading" style="color: #d1d5db; font-family: 'Syne', Arial, sans-serif; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; font-weight: 700;">
                      Progress to Grand Finale:
                    </td>
                    <td align="right" class="font-display" style="color: #00c853; font-family: 'Bebas Neue', Arial, sans-serif; font-size: 15px; font-weight: 700; letter-spacing: 1px;">
                      {$totalPoints} / 100 PTS ({$progressPct}%)
                    </td>
                  </tr>
                </table>
                <div style="width: 100%; height: 12px; background-color: #111714; border: 1px solid #1f2937; border-radius: 6px; overflow: hidden; margin-top: 6px;">
                  <div style="width: {$progressPct}%; height: 100%; background: linear-gradient(90deg, #00c853 0%, #facc15 100%);"></div>
                </div>
              </div>

              {$milestoneHtml}

              <!-- ACTION BUTTONS -->
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 24px 0 14px 0;">
                <tr>
                  <td align="center">
                    <a href="https://tnhng.com/leaderboard" class="font-display" style="display: inline-block; background-color: #00c853; color: #000000; font-family: 'Bebas Neue', 'Syne', 'Arial Black', sans-serif; font-size: 16px; font-weight: 700; text-decoration: none; padding: 12px 28px; border-radius: 4px; text-transform: uppercase; letter-spacing: 1.5px; margin: 6px;">
                      Check Live Clan Rank
                    </a>
                    <a href="https://tnhng.com/outlets" class="font-display" style="display: inline-block; background-color: transparent; border: 2px solid #00c853; color: #00c853; font-family: 'Bebas Neue', 'Syne', 'Arial Black', sans-serif; font-size: 16px; font-weight: 700; text-decoration: none; padding: 10px 24px; border-radius: 4px; text-transform: uppercase; letter-spacing: 1.5px; margin: 6px;">
                      Play at Next Outlet
                    </a>
                  </td>
                </tr>
              </table>

            </td>
          </tr>

          <!-- FOOTER -->
          <tr>
            <td>
              {$footerHtml}
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;

    $altBody = "+{$awardedPoints} PTS Banked at {$outletName}!\n\nHighlandah: {$fullName} [{$clanId}]\nChallenge: {$challengeName}\nTotal Points Now: {$totalPoints} PTS\nTarget: 100 PTS to Grand Finale Table Draw\n\nTrack live rankings: https://tnhng.com/leaderboard\n\nNO RULES. GREAT SCOTCH.\nStrictly 18+ Drink Responsibly";

    // Primary delivery attempt via SMTP
    try {
        $mail = createMailerInstance();
        $mail->addAddress($participant['email'], $participant['full_name']);
        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body    = $htmlBody;
        $mail->AltBody = $altBody;
        attachBrandLogo($mail);

        $mail->send();
        return true;
    } catch (Exception $smtpEx) {
        error_log("PHPMailer SMTP failed ({$mail->ErrorInfo}), attempting native mail() fallback...");
        try {
            $fallback = new PHPMailer(true);
            $fallback->isMail();
            $fallback->setFrom('noreply@bayeapp.com', "William Lawson's Nigeria");
            $fallback->addReplyTo('noreply@bayeapp.com', "William Lawson's Nigeria");
            $fallback->addAddress($participant['email'], $participant['full_name']);
            $fallback->Subject = $subject;
            $fallback->isHTML(true);
            $fallback->Body    = $htmlBody;
            $fallback->AltBody = $altBody;
            $fallback->CharSet = 'UTF-8';
            attachBrandLogo($fallback);

            $fallback->send();
            return true;
        } catch (Exception $e2) {
            error_log("Both SMTP and native mail() failed for {$participant['email']}: " . $e2->getMessage());
            return false;
        }
    }
}
