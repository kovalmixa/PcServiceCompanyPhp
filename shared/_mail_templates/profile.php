<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: #f4f7f6;
            padding: 30px 20px;
            margin: 0;
        }

        .card {
            background: #ffffff;
            border-radius: 16px;
            padding: 35px;
            max-width: 550px;
            margin: 0 auto;
            border: 1px solid #eef2f5;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }

        .header-block {
            border-bottom: 2px solid #f4f6f8;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }

        .title {
            font-size: 24px;
            font-weight: 700;
            color: #1a1f36;
            margin: 0 0 6px 0;
        }

        .subtitle {
            color: #8792a2;
            margin: 0;
            font-size: 14px;
            font-weight: 500;
        }

        .profile-list {
            background: #ffffff;
        }

        .row {
            padding: 16px 0;
            border-bottom: 1px solid #f4f6f8;
        }
        
        .row:last-child {
            border-bottom: none;
        }

        .label-text {
            font-size: 12px;
            text-transform: uppercase;
            color: #8792a2;
            font-weight: 700;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }

        .value-text {
            font-size: 16px;
            font-weight: 600;
            color: #1a1f36;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            text-transform: capitalize;
        }
        
        .badge-admin {
            background: #eef2ff;
            color: #4f46e5;
        }
        
        .badge-customer {
            background: #ecfdf5;
            color: #059669;
        }
        
        .badge-default {
            background: #f3f4f6;
            color: #4b5563;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #acb5c1;
            text-align: center;
            font-weight: 500;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="header-block">
        <h1 class="title">User Profile</h1>
        <p class="subtitle">Profile information exported from PC Store</p>
    </div>

    <div class="profile-list">
        <div class="row">
            <div class="label-text">Name</div>
            <div class="value-text"><?= htmlspecialchars($user['name'] ?? '—') ?></div>
        </div>

        <div class="row">
            <div class="label-text">Email Address</div>
            <div class="value-text">
                <?= htmlspecialchars($user['email'] ?? '—') ?>
            </div>
        </div>

        <div class="row">
            <div class="label-text">Phone Number</div>
            <div class="value-text">
                <?= htmlspecialchars($user['phone'] ?? '—') ?>
            </div>
        </div>

        <div class="row">
            <div class="label-text">Account Role</div>
            <div class="value-text">
                <?= htmlspecialchars($role_label ?? '—') ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>