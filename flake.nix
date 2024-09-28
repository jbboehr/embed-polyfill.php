# Copyright (c) anno Domini nostri Jesu Christi MMXVI-MMXXIV John Boehr & contributors
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Affero General Public License for more details.
#
# You should have received a copy of the GNU Affero General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.
{
  description = "jbboehr/embed-polyfill.php";

  inputs = {
    nixpkgs.url = "github:nixos/nixpkgs/nixos-24.05";
    nixpkgs-unstable.url = "github:nixos/nixpkgs/nixos-unstable";
    flake-utils = {
      url = "github:numtide/flake-utils";
    };
    pre-commit-hooks = {
      url = "github:cachix/pre-commit-hooks.nix";
      inputs.nixpkgs.follows = "nixpkgs";
    };
    gitignore = {
      url = "github:hercules-ci/gitignore.nix";
      inputs.nixpkgs.follows = "nixpkgs";
    };
    nix-github-actions = {
      url = "github:nix-community/nix-github-actions";
      inputs.nixpkgs.follows = "nixpkgs";
    };
  };

  outputs = {
    self,
    nixpkgs,
    nixpkgs-unstable,
    flake-utils,
    pre-commit-hooks,
    gitignore,
    nix-github-actions,
  }:
    flake-utils.lib.eachDefaultSystem (system: let
      buildEnv = php:
        php.buildEnv {
          extraConfig = "memory_limit = 2G";
          extensions = {
            enabled,
            all,
          }:
            enabled ++ [all.pcov];
        };
      pkgs = nixpkgs.legacyPackages.${system};
      pkgs-unstable = nixpkgs-unstable.legacyPackages.${system};

      src = gitignore.lib.gitignoreSource ./.;

      pre-commit-check = pre-commit-hooks.lib.${system}.run {
        inherit src;
        hooks = {
          actionlint.enable = true;
          alejandra.enable = true;
          alejandra.excludes = ["\/vendor\/"];
          shellcheck.enable = true;
        };
      };

      makeShell = {php}:
        pkgs.mkShell {
          buildInputs = with pkgs; [
            actionlint
            alejandra
            mdl
            php
            php.packages.composer
            pre-commit
          ];
          shellHook = ''
            ${pre-commit-check.shellHook}
            export PATH="$PWD/vendor/bin:$PATH"
          '';
        };
    in rec {
      checks = {
        inherit pre-commit-check;
      };

      devShells = rec {
        php81 = makeShell {php = buildEnv pkgs.php81;};
        php82 = makeShell {php = buildEnv pkgs.php82;};
        php83 = makeShell {php = buildEnv pkgs.php83;};
        php84 = makeShell {php = pkgs-unstable.php84;};
        default = php81;
      };

      formatter = pkgs.alejandra;
    })
    // {
      # prolly gonna break at some point
      githubActions.matrix.include = let
        cleanFn = v: v // {name = builtins.replaceStrings ["githubActions." "checks." "x86_64-linux."] ["" "" ""] v.attr;};
      in
        builtins.map cleanFn
        (nix-github-actions.lib.mkGithubMatrix {
          attrPrefix = "checks";
          checks = nixpkgs.lib.getAttrs ["x86_64-linux"] self.checks;
        })
        .matrix
        .include;
    };
}
