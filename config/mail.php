<?php
// config/mail.php - William Lawson's "The Next Highlandah" Email Service
// Powered by PHPMailer with custom brand styling and SMTPS

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

/**
 * Returns a preconfigured PHPMailer instance.
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
    $mail->Timeout    = 15;

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

    // Default sender
    $mail->setFrom('noreply@bayeapp.com', "William Lawson's Nigeria");
    $mail->addReplyTo('noreply@bayeapp.com', "William Lawson's Nigeria");
    $mail->CharSet = 'UTF-8';

    return $mail;
}

/**
 * Generates the standardized William Lawson's email footer.
 * Matches user's exact specification: Logo centered flanked by lines on both left and right sides.
 *
 * @return string
 */
function getEmailBrandFooterHtml() {
    $siteUrl = 'https://tnhng.com';
    return '
    <!-- FOOTER SECTION -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 30px; background-color: #050d08; border-top: 2px solid #005c30;">
      <tr>
        <td style="padding: 24px 20px 20px 20px; text-align: center;">

          <!-- Centered Logo Flanked by Horizontal Lines on Both Left & Right -->
          <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 0 auto 18px auto; max-width: 520px;">
            <tr>
              <td style="width: 36%; border-bottom: 2px solid #005c30; font-size: 0; line-height: 0;">&nbsp;</td>
              <td align="center" style="width: 28%; padding: 0 12px; white-space: nowrap;">
                <img src="cid:wl_logo_badge" alt="William Lawson\'s" width="96" height="64" style="display: block; margin: 0 auto; max-width: 96px; height: auto; border: 0;" />
              </td>
              <td style="width: 36%; border-bottom: 2px solid #005c30; font-size: 0; line-height: 0;">&nbsp;</td>
            </tr>
          </table>

          <p style="margin: 0 0 8px 0; font-family: \'Arial Black\', Arial, sans-serif; font-size: 13px; letter-spacing: 2px; color: #22c55e; text-transform: uppercase;">
            NO RULES. GREAT SCOTCH.
          </p>

          <p style="margin: 0 0 12px 0; font-family: Arial, sans-serif; font-size: 11px; line-height: 16px; color: #718096;">
            The Next Highlandah is an official activation by William Lawson\'s Blended Scotch Whisky.<br />
            Track live standings & find tour venues at <a href="' . $siteUrl . '" style="color: #22c55e; text-decoration: underline;">tnhng.com</a>.
          </p>

          <!-- 18+ Drink Responsibly Tag -->
          <div style="display: inline-block; padding: 4px 10px; border: 1px solid #2d3748; border-radius: 4px; font-family: Arial, sans-serif; font-size: 10px; color: #a0aec0; letter-spacing: 1px; text-transform: uppercase;">
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

    try {
        $mail = createMailerInstance();
        $mail->addAddress($participant['email'], $participant['full_name']);
        $mail->Subject = '🔥 Welcome to The Clan! Your Highlandah Pass is Ready [' . $participant['clan_id'] . ']';

        // Embed badge logo as CID
        $logoPath = __DIR__ . '/../images/wl-logo-badge.png';
        if (file_exists($logoPath)) {
            $mail->addEmbeddedImage($logoPath, 'wl_logo_badge', 'wl-logo-badge.png', 'base64', 'image/png');
        }

        $fullName = htmlspecialchars($participant['full_name'], ENT_QUOTES, 'UTF-8');
        $clanId   = htmlspecialchars($participant['clan_id'], ENT_QUOTES, 'UTF-8');
        $phone    = htmlspecialchars($participant['phone'] ?? '', ENT_QUOTES, 'UTF-8');
        $footerHtml = getEmailBrandFooterHtml();

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome to The Clan</title>
</head>
<body style="margin: 0; padding: 0; background-color: #07100a; font-family: Arial, Helvetica, sans-serif; -webkit-font-smoothing: antialiased;">
  <!-- CONTAINER TABLE -->
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #07100a; padding: 20px 10px;">
    <tr>
      <td align="center">
        <!-- EMAIL CARD -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; background-color: #0b1a10; border: 1px solid #164326; border-radius: 8px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.6);">
          
          <!-- BRAND BANNER HEADER -->
          <tr>
            <td style="background: linear-gradient(135deg, #003822 0%, #001f13 100%); padding: 30px 24px 20px 24px; text-align: center; border-bottom: 2px solid #22c55e;">
              <span style="display: inline-block; background-color: #22c55e; color: #002818; font-weight: bold; font-size: 11px; padding: 4px 12px; border-radius: 20px; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 12px;">
                Official Highlandah Pass
              </span>
              <h1 style="margin: 0 0 6px 0; color: #ffffff; font-family: 'Arial Black', Arial, sans-serif; font-size: 26px; text-transform: uppercase; letter-spacing: 1px;">
                WELCOME TO THE CLAN
              </h1>
              <p style="margin: 0; color: #a7f3d0; font-size: 14px;">
                You are now officially registered for <strong>The Next Highlandah</strong>.
              </p>
            </td>
          </tr>

          <!-- MAIN CONTENT BODY -->
          <tr>
            <td style="padding: 28px 24px 10px 24px;">
              <p style="margin: 0 0 18px 0; color: #e5e7eb; font-size: 15px; line-height: 22px;">
                Salute, <strong>{$fullName}</strong>! You've taken the first step toward Scottish greatness. Your Clan Pass is active and ready to use at any participating bar, club, or lounge across Nigeria.
              </p>

              <!-- HIGHLANDAH PASS CARD -->
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background: #06150c; border: 2px dashed #22c55e; border-radius: 6px; margin: 20px 0; padding: 18px;">
                <tr>
                  <td align="center" style="padding: 10px 0;">
                    <div style="font-size: 11px; color: #9ca3af; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 4px;">
                      Your Unique Clan Pass ID
                    </div>
                    <div style="font-family: 'Courier New', Courier, monospace; font-size: 32px; font-weight: bold; color: #facc15; letter-spacing: 3px; background: #020904; display: inline-block; padding: 8px 20px; border-radius: 4px; border: 1px solid #facc15;">
                      {$clanId}
                    </div>
                    <div style="font-size: 12px; color: #34d399; margin-top: 8px;">
                      Phone: {$phone} &bull; Starting Balance: <strong>0 PTS</strong>
                    </div>
                  </td>
                </tr>
              </table>

              <!-- GRAND FINALE TARGET BOX -->
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #14281a; border-left: 4px solid #facc15; border-radius: 4px; margin-bottom: 24px;">
                <tr>
                  <td style="padding: 14px 16px;">
                    <div style="color: #facc15; font-weight: bold; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">
                      🎯 The 100-Point Target
                    </div>
                    <div style="color: #d1d5db; font-size: 13px; line-height: 18px;">
                      Accumulate <strong>100 points</strong> across any of our tour outlets to qualify for the Grand Finale draw and earn a VIP celebrity table experience!
                    </div>
                  </td>
                </tr>
              </table>

              <!-- HOW IT WORKS (3 STEPS) -->
              <h2 style="margin: 24px 0 14px 0; color: #ffffff; font-size: 17px; font-family: 'Arial Black', Arial, sans-serif; text-transform: uppercase; letter-spacing: 0.5px;">
                HOW TO BANK POINTS:
              </h2>

              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 24px;">
                <!-- Step 1 -->
                <tr>
                  <td width="36" valign="top" style="padding-bottom: 14px;">
                    <div style="width: 28px; height: 28px; background-color: #22c55e; color: #002818; font-weight: bold; border-radius: 50%; text-align: center; line-height: 28px; font-size: 13px;">1</div>
                  </td>
                  <td valign="top" style="padding-bottom: 14px; padding-left: 10px;">
                    <strong style="color: #ffffff; font-size: 14px;">Visit Participating Outlets</strong>
                    <p style="margin: 4px 0 0 0; color: #9ca3af; font-size: 13px; line-height: 18px;">
                      Head to any William Lawson's tour outlet across Nigeria during activation dates.
                    </p>
                  </td>
                </tr>
                <!-- Step 2 -->
                <tr>
                  <td width="36" valign="top" style="padding-bottom: 14px;">
                    <div style="width: 28px; height: 28px; background-color: #22c55e; color: #002818; font-weight: bold; border-radius: 50%; text-align: center; line-height: 28px; font-size: 13px;">2</div>
                  </td>
                  <td valign="top" style="padding-bottom: 14px; padding-left: 10px;">
                    <strong style="color: #ffffff; font-size: 14px;">Show Your Clan ID to the Ambassador</strong>
                    <p style="margin: 4px 0 0 0; color: #9ca3af; font-size: 13px; line-height: 18px;">
                      Call out <code style="color: #facc15; font-weight: bold;">{$clanId}</code> or show this email. No new registration needed!
                    </p>
                  </td>
                </tr>
                <!-- Step 3 -->
                <tr>
                  <td width="36" valign="top" style="padding-bottom: 14px;">
                    <div style="width: 28px; height: 28px; background-color: #22c55e; color: #002818; font-weight: bold; border-radius: 50%; text-align: center; line-height: 28px; font-size: 13px;">3</div>
                  </td>
                  <td valign="top" style="padding-bottom: 14px; padding-left: 10px;">
                    <strong style="color: #ffffff; font-size: 14px;">Spin the Bottle & Conquer Challenges</strong>
                    <p style="margin: 4px 0 0 0; color: #9ca3af; font-size: 13px; line-height: 18px;">
                      Take on high-energy Scottish challenges to lock in instant official scorecard points synced live.
                    </p>
                  </td>
                </tr>
              </table>

              <!-- CTA BUTTONS -->
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 10px 0 20px 0;">
                <tr>
                  <td align="center">
                    <a href="https://tnhng.com/leaderboard" style="display: inline-block; background-color: #22c55e; color: #002818; font-family: 'Arial Black', Arial, sans-serif; font-size: 14px; font-weight: bold; text-decoration: none; padding: 14px 28px; border-radius: 4px; text-transform: uppercase; letter-spacing: 1px; margin: 6px;">
                      View Live Leaderboard
                    </a>
                    <a href="https://tnhng.com/outlets" style="display: inline-block; background-color: transparent; border: 2px solid #22c55e; color: #22c55e; font-family: Arial, sans-serif; font-size: 13px; font-weight: bold; text-decoration: none; padding: 12px 24px; border-radius: 4px; text-transform: uppercase; letter-spacing: 1px; margin: 6px;">
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

        $mail->isHTML(true);
        $mail->Body    = $htmlBody;
        $mail->AltBody = "Welcome to The Clan, {$participant['full_name']}!\n\nYour Clan ID: {$participant['clan_id']}\nPhone: {$participant['phone']}\nStarting Balance: 0 PTS\n\nVisit any William Lawson's tour outlet to play and bank points toward the 100 PTS Grand Finale threshold!\nTrack live at https://tnhng.com\n\nNO RULES. GREAT SCOTCH.\nStrictly 18+ Drink Responsibly";

        try {
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
                $fallback->Subject = $mail->Subject;
                $fallback->isHTML(true);
                $fallback->Body    = $htmlBody;
                $fallback->AltBody = $mail->AltBody;
                $fallback->CharSet = 'UTF-8';
                $fallback->send();
                return true;
            } catch (Exception $e2) {
                error_log("Both SMTP and native mail() failed for {$participant['email']}: " . $e2->getMessage());
                return false;
            }
        }
    } catch (Exception $e) {
        error_log("PHPMailer error (Welcome Email to {$participant['email']}): " . $e->getMessage());
        return false;
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

    try {
        $mail = createMailerInstance();
        $mail->addAddress($participant['email'], $participant['full_name']);

        $awardedPoints = intval($allocation['points'] ?? 0);
        $outletName    = htmlspecialchars($allocation['outlet_name'] ?? 'Partner Outlet', ENT_QUOTES, 'UTF-8');
        $challengeName = htmlspecialchars($allocation['challenge'] ?? 'Highlandah Challenge', ENT_QUOTES, 'UTF-8');
        $category      = htmlspecialchars($allocation['category'] ?? 'General', ENT_QUOTES, 'UTF-8');
        $promoterName  = htmlspecialchars($allocation['promoter_name'] ?? 'Brand Ambassador', ENT_QUOTES, 'UTF-8');

        $fullName      = htmlspecialchars($participant['full_name'], ENT_QUOTES, 'UTF-8');
        $clanId        = htmlspecialchars($participant['clan_id'], ENT_QUOTES, 'UTF-8');
        $totalPoints   = intval($participant['total_points'] ?? 0);

        $mail->Subject = "⚡ +{$awardedPoints} PTS Banked at {$outletName}! [{$clanId}]";

        // Embed badge logo as CID
        $logoPath = __DIR__ . '/../images/wl-logo-badge.png';
        if (file_exists($logoPath)) {
            $mail->addEmbeddedImage($logoPath, 'wl_logo_badge', 'wl-logo-badge.png', 'base64', 'image/png');
        }

        // Calculate progress toward 100 PTS threshold
        $progressPct = min(100, max(0, round(($totalPoints / 100) * 100)));
        $pointsNeeded = max(0, 100 - $totalPoints);
        $isQualified = ($totalPoints >= 100);

        $footerHtml = getEmailBrandFooterHtml();

        // Milestone message
        if ($isQualified) {
            $milestoneHtml = '
            <div style="background-color: #064e3b; border: 2px solid #34d399; border-radius: 6px; padding: 16px; margin: 20px 0; text-align: center;">
              <div style="font-size: 20px; font-weight: bold; color: #facc15; font-family: \'Arial Black\', Arial, sans-serif; text-transform: uppercase;">
                🏆 GRAND FINALE QUALIFIED!
              </div>
              <p style="margin: 6px 0 0 0; color: #ecfdf5; font-size: 13px; line-height: 18px;">
                You have reached the <strong>100 PTS milestone</strong>! Your entry has been locked in for the celebrity VIP Table Grand Finale draw!
              </p>
            </div>';
        } else {
            $milestoneHtml = '
            <div style="background-color: #14281a; border-left: 4px solid #facc15; border-radius: 4px; padding: 12px 16px; margin: 20px 0;">
              <span style="color: #facc15; font-weight: bold; font-size: 13px; text-transform: uppercase;">
                🎯 Target Remaining:
              </span>
              <span style="color: #e5e7eb; font-size: 13px;">
                Only <strong>' . $pointsNeeded . ' more points</strong> needed to unlock the 100 PTS Grand Finale VIP Table Draw!
              </span>
            </div>';
        }

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Points Banked Notification</title>
</head>
<body style="margin: 0; padding: 0; background-color: #07100a; font-family: Arial, Helvetica, sans-serif; -webkit-font-smoothing: antialiased;">
  <!-- CONTAINER TABLE -->
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #07100a; padding: 20px 10px;">
    <tr>
      <td align="center">
        <!-- EMAIL CARD -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; background-color: #0b1a10; border: 1px solid #164326; border-radius: 8px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.6);">
          
          <!-- BANNER HEADER -->
          <tr>
            <td style="background: linear-gradient(135deg, #003822 0%, #001f13 100%); padding: 28px 24px 20px 24px; text-align: center; border-bottom: 2px solid #22c55e;">
              <span style="display: inline-block; background-color: #facc15; color: #002818; font-weight: bold; font-size: 11px; padding: 4px 12px; border-radius: 20px; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 10px;">
                Live Scorecard Sync
              </span>
              <h1 style="margin: 0 0 6px 0; color: #ffffff; font-family: 'Arial Black', Arial, sans-serif; font-size: 26px; text-transform: uppercase; letter-spacing: 1px;">
                POINTS BANKED!
              </h1>
              <p style="margin: 0; color: #a7f3d0; font-size: 14px;">
                Your scorecard was updated at <strong>{$outletName}</strong>
              </p>
            </td>
          </tr>

          <!-- MAIN BODY -->
          <tr>
            <td style="padding: 26px 24px 10px 24px;">
              <p style="margin: 0 0 18px 0; color: #e5e7eb; font-size: 15px; line-height: 22px;">
                Highlandah <strong>{$fullName}</strong> (<code>{$clanId}</code>), your challenge results were verified by Ambassador <strong>{$promoterName}</strong>!
              </p>

              <!-- SCORECARD SUMMARY CARD -->
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #05140b; border: 1px solid #1e3a27; border-radius: 6px; margin-bottom: 20px;">
                <tr>
                  <td style="padding: 18px 20px; text-align: center; border-bottom: 1px solid #164326;">
                    <div style="font-size: 11px; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">
                      Points Awarded
                    </div>
                    <div style="font-family: 'Arial Black', Arial, sans-serif; font-size: 38px; color: #22c55e; line-height: 42px;">
                      +{$awardedPoints} <span style="font-size: 18px; color: #facc15;">PTS</span>
                    </div>
                    <div style="font-size: 13px; color: #e5e7eb; margin-top: 4px;">
                      Challenge: <strong>{$challengeName}</strong> ({$category})
                    </div>
                  </td>
                </tr>
                <tr>
                  <td style="padding: 16px 20px; background-color: #081a10;">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                      <tr>
                        <td style="color: #9ca3af; font-size: 13px;">Activation Outlet:</td>
                        <td align="right" style="color: #22c55e; font-size: 14px; font-weight: bold;">📍 {$outletName}</td>
                      </tr>
                      <tr>
                        <td style="color: #9ca3af; font-size: 13px; padding-top: 8px;">Total Points Now:</td>
                        <td align="right" style="color: #facc15; font-size: 18px; font-weight: bold; padding-top: 8px;">{$totalPoints} PTS</td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              <!-- PROGRESS BAR -->
              <div style="margin: 22px 0 10px 0;">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                  <tr>
                    <td style="color: #d1d5db; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; font-weight: bold;">
                      Progress to Grand Finale:
                    </td>
                    <td align="right" style="color: #22c55e; font-size: 12px; font-weight: bold;">
                      {$totalPoints} / 100 PTS ({$progressPct}%)
                    </td>
                  </tr>
                </table>
                <div style="width: 100%; height: 12px; background-color: #1f2937; border-radius: 6px; overflow: hidden; margin-top: 6px;">
                  <div style="width: {$progressPct}%; height: 100%; background: linear-gradient(90deg, #22c55e 0%, #facc15 100%);"></div>
                </div>
              </div>

              {$milestoneHtml}

              <!-- ACTION BUTTONS -->
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 24px 0 14px 0;">
                <tr>
                  <td align="center">
                    <a href="https://tnhng.com/leaderboard" style="display: inline-block; background-color: #22c55e; color: #002818; font-family: 'Arial Black', Arial, sans-serif; font-size: 14px; font-weight: bold; text-decoration: none; padding: 14px 28px; border-radius: 4px; text-transform: uppercase; letter-spacing: 1px; margin: 6px;">
                      Check My Leaderboard Rank
                    </a>
                    <a href="https://tnhng.com/outlets" style="display: inline-block; background-color: transparent; border: 2px solid #22c55e; color: #22c55e; font-family: Arial, sans-serif; font-size: 13px; font-weight: bold; text-decoration: none; padding: 12px 24px; border-radius: 4px; text-transform: uppercase; letter-spacing: 1px; margin: 6px;">
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

        $mail->isHTML(true);
        $mail->Body    = $htmlBody;
        $mail->AltBody = "+{$awardedPoints} PTS Banked at {$outletName}!\n\nHighlandah: {$fullName} [{$clanId}]\nChallenge: {$challengeName}\nTotal Points Now: {$totalPoints} PTS\nTarget: 100 PTS to Grand Finale\n\nTrack live rankings: https://tnhng.com/leaderboard\n\nNO RULES. GREAT SCOTCH.\nStrictly 18+ Drink Responsibly";

        try {
            $mail->send();
            return true;
        } catch (Exception $smtpEx) {
            error_log("PHPMailer SMTP failed ({$mail->ErrorInfo}), attempting native mail() fallback...");
            try {
                $fallback = new PHPMailer(true);
                $fallback->isMail();
                $fallback->setFrom('noreply@bayeapp.com', "William Lawson's Nigeria");
                $fallback->addReplyTo('noreply@bayeapp.com', "William Lawson's Nigeria");
                $fallback->addAddress($participant['email'], $fullName);
                $fallback->Subject = $mail->Subject;
                $fallback->isHTML(true);
                $fallback->Body    = $htmlBody;
                $fallback->AltBody = $mail->AltBody;
                $fallback->CharSet = 'UTF-8';
                $fallback->send();
                return true;
            } catch (Exception $e2) {
                error_log("Both SMTP and native mail() failed for {$participant['email']}: " . $e2->getMessage());
                return false;
            }
        }
    } catch (Exception $e) {
        error_log("PHPMailer error (Points Email to {$participant['email']}): " . $e->getMessage());
        return false;
    }
}
