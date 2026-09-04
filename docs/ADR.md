# Architecture Decision Log

Newest at the bottom. Never edit a past entry — add a new one instead. All entries here are Accepted.

---

## ADR 01: Use PHP as the template's development language

Language choice was left open initially, but a devcontainer and CI checks need a concrete language to target, so PHP is the language developers write in inside the devcontainer (toolchain, linting, tests, CI) — the devcontainer needs PHP + Composer, linting/static analysis via PHP-CS-Fixer/PHPStan, tests via PHPUnit, and CI needs a PHP setup step; the language used to build the template's own scaffolding tooling is a separate, still-open decision.

---

## ADR 02: Toolchain isolation: devcontainer built from a Nix flake

Nix and devcontainers solve different layers of the same problem — Nix pins exact tool versions with a real reproducibility guarantee (`flake.lock`, see [docs/RESEARCH.md](RESEARCH.md)), while devcontainers give the IDE integration and one-click "reopen in container" onboarding that Nix alone lacks. Rather than choosing one, the devcontainer's Dockerfile installs Nix and runs `nix develop` (or builds the image from the flake directly), so contributors get plug-and-play onboarding while the environment itself stays hash-pinned. Contributors using the devcontainer never need to touch Nix syntax directly; only the flake's author does. Only one of the two is needed locally, not both: Docker alone gets you the devcontainer (Nix runs inside it), or Nix alone gets you `nix develop` directly — a contributor can also skip both and use their own local PHP/Composer, at the cost of losing the reproducibility guarantee.

---

## ADR 03: Scaffolding tool: use Copier (Python), not a custom PHP tool

PHP would have been the natural, native choice here — it's already the template's language per ADR 01, and picking it for the scaffolder too would have kept everything in one stack. It wasn't chosen because PHP's templating ecosystem has no equivalent to `copier update` (see [docs/RESEARCH.md](RESEARCH.md)): re-rendering the template and merging its changes onto already-generated repos via git, so downstream projects can pull in template improvements later instead of staying frozen at day-0. No maintained PHP package does this, and building it ourselves is real, well-tested engineering effort we'd rather not take on. Copier already provides it, is what the reference template ([sdsc-ordes/repository-template](https://github.com/sdsc-ordes/repository-template)) uses, and is consumed as a pinned CLI dependency (like any other tool in the devcontainer) rather than something we author — it does not change ADR 01: PHP is still the language developers write in inside the generated project. If a PHP tool ever gains equivalent update capability, this decision should be revisited.

---

## ADR 04: Automated checks scope: PHP-CS-Fixer, PHPStan, PHPUnit/Pest only

Of the checks considered (formatting, static analysis, tests, dependency/security audit, CI wiring), only the de facto PHP standards are in scope for now: **PHP-CS-Fixer** for formatting, **PHPStan** for static analysis/types (catches AI-plausible-but-wrong code before it ships), and **PHPUnit** (or **Pest**, see ADR 05) for tests. Dependency/security auditing (`composer audit`, hallucinated-dependency detection) and any git-hook wiring are deliberately deferred — not rejected, just out of scope for this pass. Revisit if security/dependency checks need to be demonstrated explicitly.

---

## ADR 05: Generated project structure

The scaffolded PHP project follows a conventional layout (matching common PHP/Symfony practice, not invented): `src/` for application code, `composer.json` for dependency management, `tests/` for test code, `public/` as the web root for publicly served files, `config/` for application configuration, `.env`/`.env.example` for environment variables, `bin/` for CLI entry points, and `var/` for logs and cache (git-ignored). Test framework is a Copier prompt (`phpunit` or `pest`) rather than a fixed choice — Pest runs on top of PHPUnit, so both write to the same `tests/` folder and only the config/dependency differs by which is templated in.

---

## ADR 06: AI coding rules grounded in applicable 12-Factor App principles

`AGENTS.md` needs concrete coding rules, not just process rules — and the 12-Factor App methodology already matches this structure closely (`config/`+`.env` for Config, `composer.json` for Dependencies, `bin/` for Admin processes, `var/` for disposable cache, the devcontainer/Nix setup for Dev/prod parity). Only the factors that are concretely actionable for a blank template are included — Dependencies, Config, Dev/prod parity, Logs, Admin processes, Disposability — not all twelve; factors like Backing services, Build/release/run, Port binding, and Concurrency don't have anything concrete to say until the project actually has a backing service or a deploy target, so stating them now would be unenforceable and would violate the "keep docs short" rule with guidance nobody can act on yet.
