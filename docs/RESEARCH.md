# Research Notes

Things learned while researching this project.

## Nix

In the Linux world, a package "version" keeps getting silent underlying updates
(security patches, rebuilds, bug fixes) even when its name/tag stays the same,
so two installs months apart can differ. Nix fixes this by naming every build
by a hash of its exact recipe (source + dependencies + build steps) and
freezing that recipe's exact reference (`nixpkgs` commit) in a committed
`flake.lock` — so anyone building from the same lockfile gets bit-identical
results forever, and versions only change via an explicit, reviewable
`nix flake update` diff. Nixpkgs (the recipes) and `cache.nixos.org` (a real
binary cache of pre-built results, keyed by hash) are separate systems, so
builds are usually downloaded pre-built rather than recompiled.

## Dev Containers

An open spec (`containers.dev`) where a `.devcontainer/devcontainer.json`
(plus a Dockerfile or `docker-compose.yml`) fully describes a dev environment
as a container — base image, tools, extensions, setup commands. VS Code,
GitHub Codespaces, and JetBrains all support "reopen this repo in its
container," so a new developer's setup becomes "install Docker, click reopen"
instead of a manual install guide, and the same container can be reused in CI
for local/CI parity. Reproducibility is weaker than Nix by default — no single
lockfile pins everything, so it depends on manually pinning the base image
digest and package versions (see the Nix vs. Docker discussion above) — but
it's simpler and needs no new language, since it's just Docker underneath.

## Copier's Update Mechanism

A template-generated repo is normally a one-time snapshot: if the template
later improves, existing projects have no way to receive that except manual
copy-paste. Copier fixes this with `copier update`. On first generation it
writes a hidden `.copier-answers.yml` recording the template's git ref and the
prompt answers used. To update, it re-renders the template at the old ref and
at the new ref using those same answers, then merges the *diff between those
two renders* onto the user's actual (possibly hand-edited) repo — using real
`git merge`, not a custom algorithm, so conflicts show up as normal merge
conflicts. This only works because the answers file exists; reimplementing it
would mean the same git-merge orchestration, which is buildable in any
language (git is just a CLI tool) but is real, well-tested engineering effort
that a maintained tool already provides for free. 
