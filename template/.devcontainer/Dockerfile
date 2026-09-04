FROM debian:bookworm-slim

RUN apt-get update \
    && apt-get install -y --no-install-recommends curl ca-certificates xz-utils git openssh-client sudo direnv \
    && rm -rf /var/lib/apt/lists/*

# System-wide, so it applies no matter which user (root or the container user)
# actually invokes `nix` — per-user config alone is fragile since
# postCreateCommand's effective user isn't always the one you'd expect.
RUN mkdir -p /etc/nix \
    && echo "experimental-features = nix-command flakes" > /etc/nix/nix.conf

ARG USERNAME=vscode
RUN useradd -m -s /bin/bash "${USERNAME}" \
    && echo "${USERNAME} ALL=(ALL) NOPASSWD:ALL" > /etc/sudoers.d/${USERNAME}

USER ${USERNAME}
WORKDIR /home/${USERNAME}

# Installs Nix itself; the actual PHP toolchain comes from ./flake.nix (ADR 02),
# not from apt, so it stays hash-pinned instead of drifting with the base image.
RUN curl -L https://nixos.org/nix/install | sh -s -- --no-daemon \
    && echo ". /home/${USERNAME}/.nix-profile/etc/profile.d/nix.sh" >> /home/${USERNAME}/.bashrc \
    && echo 'eval "$(direnv hook bash)"' >> /home/${USERNAME}/.bashrc

ENV PATH="/home/${USERNAME}/.nix-profile/bin:${PATH}"
