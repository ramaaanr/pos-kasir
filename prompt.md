🔹 SUBJECT

Anda adalah seorang ahli di bidang pengembangan menggunakna Laravel 12 terlebih untuk fullstack developer serta membanung aplikasi kasir

🔹 OBJECTIVE

Membuat UI Login menjadi lebih menarik berdasrakna contoh react yang saya berikan

🔹 CONTEXT

Halaman Login sdh ada tapi saya ingin merubahnya agar mirip dengan kode dibawah yang berbasis react
return (
    <div className="min-h-screen flex items-center justify-center bg-background p-4">
      {/* Background decoration */}
      <div className="absolute inset-0 overflow-hidden pointer-events-none">
        <div className="absolute -top-40 -right-40 w-80 h-80 bg-primary/10 rounded-full blur-3xl" />
        <div className="absolute -bottom-40 -left-40 w-80 h-80 bg-accent/10 rounded-full blur-3xl" />
      </div>

      <Card className="w-full max-w-md glass animate-fade-in relative z-10">
        <CardHeader className="text-center space-y-4">
          <div className="mx-auto w-16 h-16 rounded-2xl gradient-admin flex items-center justify-center shadow-lg animate-pulse-glow">
            <Store className="w-8 h-8 text-primary-foreground" />
          </div>
          <div>
            <CardTitle className="text-2xl font-display font-bold">
              POS System
            </CardTitle>
            <CardDescription className="mt-2">
              Masuk ke akun Anda untuk melanjutkan
            </CardDescription>
          </div>
        </CardHeader>

        <CardContent>
          <form onSubmit={handleSubmit} className="space-y-5">
            <div className="space-y-2">
              <Label htmlFor="email" className="text-sm font-medium">
                Email
              </Label>
              <div className="relative">
                <Mail className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                <Input
                  id="email"
                  type="email"
                  placeholder="nama@perusahaan.com"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  className="pl-10"
                  disabled={isLoading}
                  autoComplete="email"
                />
              </div>
              {errors.email && (
                <p className="text-sm text-destructive">{errors.email}</p>
              )}
            </div>

            <div className="space-y-2">
              <Label htmlFor="password" className="text-sm font-medium">
                Password
              </Label>
              <div className="relative">
                <Lock className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                <Input
                  id="password"
                  type={showPassword ? 'text' : 'password'}
                  placeholder="••••••••"
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  className="pl-10 pr-10"
                  disabled={isLoading}
                  autoComplete="current-password"
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors"
                  disabled={isLoading}
                >
                  {showPassword ? (
                    <EyeOff className="w-4 h-4" />
                  ) : (
                    <Eye className="w-4 h-4" />
                  )}
                </button>
              </div>
              {errors.password && (
                <p className="text-sm text-destructive">{errors.password}</p>
              )}
            </div>

            <Button
              type="submit"
              className="w-full font-medium"
              disabled={isLoading}
            >
              {isLoading ? (
                <div className="flex items-center gap-2">
                  <div className="w-4 h-4 border-2 border-primary-foreground border-t-transparent rounded-full animate-spin" />
                  <span>Memproses...</span>
                </div>
              ) : (
                'Masuk'
              )}
            </Button>
          </form>

          {/* Demo accounts info */}
          <div className="mt-6 p-4 rounded-lg bg-muted/50 border border-border/50">
            <p className="text-xs font-medium text-muted-foreground mb-2">
              Akun Demo:
            </p>
            <div className="space-y-1 text-xs text-muted-foreground">
              <p><span className="font-medium">Admin:</span> admin@pos.com / admin123</p>
              <p><span className="font-medium">Kasir:</span> kasir@pos.com / kasir123</p>
              <p><span className="font-medium">Owner:</span> owner@pos.com / owner123</p>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>

    saya ingin kamu mengambil kode tailwindnya saja serta layoutnya dan jangan implementasikan react karena kita menggunakna vanilla js dengan tailwind saja

🔹 CONSTRAINT

The implementation must comply with all constraints below:
- jangan ubah sistem login input
- fokus ke tampilan
- jangan buat kode react, anda bertugas untuk translate react ke vanilla js dengan tailwindnya saja
 Output

 Tampilan login yang menarik sesuai dengan react tersebut