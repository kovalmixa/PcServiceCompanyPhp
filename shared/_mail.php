<?php
require_once __DIR__ . '/_helpers.php';

function renderMailTemplate(string $template, array $data = []): string
{
    $templatePath = __DIR__ . "/_mail_templates/{$template}.php";
    if (!file_exists($templatePath)) throw new Exception("Mail template '{$template}' not found.");
    extract($data);
    ob_start();
    include $templatePath;
    return ob_get_clean();
}

function sendMail(string $to, string $subject, string $htmlContent): bool
{
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=utf-8',
        'From: PC Store <kovalchukmixa@gmail.com>',
        'Reply-To: kovalchukmixa@gmail.com',
        'X-Mailer: PHP/' . phpversion()
    ];

    $result = @mb_send_mail(
        $to,
        $subject,
        $htmlContent,
        implode("\r\n", $headers)
    );

    return $result;
}

function handleMailRequest(string $subject, string $template, array $data = []): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
    if (!isset($_POST['action_send_mail'])) return;
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');
    try {
        $to = filter_var(trim($_POST['email_to'] ?? ''),FILTER_VALIDATE_EMAIL);

        if (!$to) throw new Exception("Invalid email address.");
        $html = renderMailTemplate($template, $data);

        if (!$html) throw new Exception("Template render failed.");
        if (!sendMail($to, $subject, $html)) throw new Exception("Mail server error.");
        echo json_encode(['success' => true,'message' => "Mail successfully sent to {$to}"]);
    } catch (Exception $e) {
        echo json_encode(['success' => false,'error' => $e->getMessage()]);
    }
    exit;
}

function renderMailWidget(array $config): void {
    $subject = $config['subject'] ?? 'Mail';
    $template = $config['template'] ?? '';
    $data = $config['data'] ?? [];
    handleMailRequest($subject, $template, $data);
    ?>

    <div class="email-card" style="width:100%;">
        <form class="mail-form"
                method="POST"
                style="display:flex;flex-direction:column;gap:8px;">
            <input type="hidden"name="action_send_mail"value="1">
            <div style="display:flex;flex-direction:column;gap:4px;">
                <input type="email"
                        name="email_to"
                        required
                        placeholder="example@mail.com"
                        style="
                            background:rgba(0,0,0,0.05);
                            border:1px solid #bbb;
                            border-radius:8px;
                            padding:10px;
                            outline:none;
                            width:100%;
                            box-sizing:border-box;
                        ">
            </div>
            <button type="submit" class="a-btn" style="width:100%;padding:10px;font-weight:bold;">
                Send Email
            </button>
        </form>
    </div>

    <script>
    document.querySelectorAll('.mail-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            try {
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    alert(data.message);
                    form.reset();
                } else alert(data.error || 'Unknown error.');
            } catch (err) {
                console.error(err);
                alert('Request failed.');
            }
        });
    });
    </script>
    <?php
}