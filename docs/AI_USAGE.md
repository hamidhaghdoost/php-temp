# AI Usage Disclaimer

This project was built in an interactive session with Claude (Anthropic). This
is a factual description of how the work was split — not a claim about what
the reader should trust; adjust the "human-owned" section if it overstates or
understates your own understanding before treating it as final.

## How this was actually built

Every architectural decision — language choice, the Nix+devcontainer isolation
approach, Copier for scaffolding, which checks to run, the project structure,
supporting both PHPUnit and Pest, the 12-Factor-grounded coding rules — was
made in conversation and recorded with its reasoning in [docs/ADR.md](ADR.md).
Nothing was accepted as "AI suggested it, so it's in" — each decision has a
stated why, and several (Copier vs. a custom PHP scaffolder, Nix vs.
devcontainer-only) were chosen after explicitly discussing the alternative.

Testing was not rubber-stamped: the devcontainer, Nix flake, and Copier
template were run for real (locally by the AI where tooling allowed — Copier
via a throwaway pip install, since Nix wasn't available in that environment —
and inside an actual VS Code devcontainer by the human, where Nix was
available). Real failures surfaced this way and got fixed, not glossed over:
Nix's flake feature being disabled by default, a `composer.lock` locked to a
PHP version incompatible with the pinned container PHP, PHP-CS-Fixer's risky
rules needing explicit opt-in, and a Nix flake hanging because the project
directory wasn't a git repo yet.

## Human-owned

- All scope and architecture decisions in `docs/ADR.md` (01–06) — the choice
  itself and the reasoning were directed by the human, not generated
  unprompted by the AI.
- The decision to defer specific items (pre-commit hooks, dependency/security
  auditing, GitLab CI, `flake.lock` generation) rather than build them now.
- Diagnosis of real runtime failures reported from actually running the
  devcontainer/Nix setup — these were genuine bugs, not hypothetical review.
- The AI-generated template output was reviewed, then extended on top of that
  base with additional features that were not part of the initial scaffold:
  environment-variable handling (`phpdotenv`, `.env.example`, `APP_NAME`) and
  structured logging (`monolog`, `config/logger.php`, the stdout/file switch).

## AI-assisted (written by Claude, reviewed through real testing above)

- All template files under `template/` — Dockerfile, `flake.nix`, `copier.yml`,
  `composer.json`, PHP source/config stubs, CI workflow.
- Prose in `docs/ADR.md`, `docs/RESEARCH.md`, and this file.

Nothing in this project is marked "AI-generated, non-owned" — everything above
was exercised by actually running it (rendering the template, installing
dependencies, running the checks, opening the devcontainer) and fixing what
broke, rather than left unreviewed.
