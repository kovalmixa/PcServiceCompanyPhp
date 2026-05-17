<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: sans-serif;
            background: #f4f7f6;
            padding: 20px;
            color: #111;
        }

        .card {
            background: #fff;
            border-radius: 16px;
            padding: 30px;
            max-width: 1000px;
            margin: 0 auto;
            border: 1px solid #ddd;
        }

        .title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #777;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .stats {
            margin-bottom: 25px;
            padding: 18px;
            border-radius: 12px;
            background: #f8f8f8;
            border: 1px solid #e5e5e5;
        }

        .stats-label {
            font-size: 12px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .stats-value {
            font-size: 24px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f3f3f3;
        }

        th {
            text-align: left;
            padding: 14px;
            font-size: 12px;
            text-transform: uppercase;
            color: #666;
        }

        td {
            padding: 14px;
            border-top: 1px solid #eee;
        }

        .user-name {
            font-weight: bold;
        }

        .role-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 999px;
            background: #efefef;
            font-size: 12px;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="title">User Management Report</div>
    <div class="subtitle">Exported users list from PC Store </div>
    <div class="stats">
        <div class="stats-label">Total Users</div>
        <div class="stats-value"><?= count($users ?? []) ?></div>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach (($users ?? []) as $user): ?>
            <tr>
                <td>#<?= (int)$user['id'] ?></td>
                <td><div class="user-name"><?= htmlspecialchars($user['name']) ?></div></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td>
                    <span class="role-badge">
                        <?=
                            htmlspecialchars(
                                is_object($user['role'])
                                    ? $user['role']->label()
                                    : $user['role']
                            )
                        ?>
                    </span>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>