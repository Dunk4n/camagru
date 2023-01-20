<div class="main">
    <div class="alert alert-warning">
        You need to verify your account.
        Sign in to your email account and click on the
        verification link we just emailed you at
        <strong><?php echo $_SESSION['email']; ?></strong>
    </div>
    <a href="index.php?resend=1" class="logout">resend email</a>
</div>
