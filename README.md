# PHP TEMP

Design decisions and their rationale are tracked in [docs/ADR.md](docs/ADR.md).
Research notes are in [docs/RESEARCH.md](docs/RESEARCH.md).
AI usage disclaimer: [docs/AI_USAGE.md](docs/AI_USAGE.md).

## Goals

- **Reproducibility** — the same environment and checks every time, for every developer and in CI, with no "works on my machine" drift.
- **Isolation** — the dev toolchain stays sandboxed from the host machine.
- **AI-readiness** — clear rules and context so AI coding tools behave predictably.
- **Safeguards** — automated checks that catch bad output, AI-generated or not.
- **Usability** — easy to instantiate and adapt without guesswork.

## Usage

**1. Clone this template.**

```sh
git clone https://github.com/hamidhaghdoost/php-temp.git
cd php-temp
```

**2. Generate a new project with [Copier](https://copier.readthedocs.io).**

```sh
# With Nix (no local Python/pip needed):
nix run nixpkgs#copier -- copy . ../my-new-app

# Or with pipx/uvx:
pipx run copier copy . ../my-new-app
# (if `copier` isn't found after a plain `pip install`, use `python3 -m copier` instead)
```

You'll be prompted for project name, description, PHP version, and test framework
(PHPUnit or Pest).

**3. Turn the generated project into its own git repo.** This is required, not
optional — the Nix flake needs git tracking to resolve, or `nix develop` hangs
trying to copy the whole untracked directory into the store.

```sh
cd ../my-new-app
git init
git add -A
git commit -m "Initial commit"
```

**4. Get an isolated PHP toolchain** — pick one (see ADR 02):

- **Devcontainer** (only needs Docker): open the folder in VS Code, "Reopen in
  Container." First run installs Nix inside the container and runs
  `composer install` automatically.
- **Nix directly**: `nix develop` (or just open a new terminal in the folder —
  `.envrc` + direnv auto-load it).
- **Neither**: use your own local PHP/Composer — works, but without the
  reproducibility guarantee.

**5. Set up the environment and install dependencies** (skip `composer install`
if the devcontainer already ran it):

```sh
cp .env.example .env
composer install
```

**6. Run it.**

```sh
composer serve
# visit http://localhost:8000
```

**7. Run the checks** (same ones CI runs):

```sh
composer format:check   # PHP-CS-Fixer
composer analyse        # PHPStan
composer test           # PHPUnit or Pest
```

**8. Push to your own GitHub/GitLab repo.** `.github/workflows/ci.yml` runs the
checks above automatically on every push/PR.

**9. Later, pull in template improvements** with `copier update` inside the
generated project (see [docs/RESEARCH.md](docs/RESEARCH.md) for how that works).

## Future Improvements

Deliberately deferred, not overlooked (see ADR 04):

- **Pre-commit hook for PHP-CS-Fixer** — right now formatting is only enforced in CI, so unformatted code can still be committed locally and only gets caught later.
- **Dependency/security auditing** — `composer audit` and stronger hallucinated-dependency detection beyond "CI fails if `composer install` can't resolve a package."
- **GitHub branch protection** — CI currently fails loudly on violations but doesn't block merging; that needs a repo setting, not a template file.
- **Commit `flake.lock`** — not yet generated in this repo, since building it requires Nix, which isn't available in the environment this template was authored in.
- **GitLab CI coverage** — only a GitHub Actions workflow exists; GitLab users currently have no equivalent `.gitlab-ci.yml`.
