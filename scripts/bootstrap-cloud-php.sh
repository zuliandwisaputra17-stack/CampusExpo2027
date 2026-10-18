#!/usr/bin/env bash
# Optional, unprivileged bootstrap for the Debian Codex cloud image.
set -euo pipefail
TOOL_ROOT=/workspace/tooling
mkdir -p "$TOOL_ROOT/bin" "$TOOL_ROOT/php" "$TOOL_ROOT/apt/lists/partial" "$TOOL_ROOT/apt/archives/partial"
if ! command -v php >/dev/null 2>&1 && [[ ! -x "$TOOL_ROOT/bin/php" ]]; then
  cat > "$TOOL_ROOT/apt/apt.conf" <<'CONF'
Dir::Etc::parts "-";
Dir::Etc::main "-";
Dir::Etc::sourcelist "/workspace/tooling/apt/sources.list";
Dir::Etc::sourceparts "-";
Dir::State::lists "/workspace/tooling/apt/lists";
Dir::Cache::archives "/workspace/tooling/apt/archives";
Dir::State::status "/var/lib/dpkg/status";
APT::Get::List-Cleanup "0";
CONF
  cat > "$TOOL_ROOT/apt/sources.list" <<'SOURCES'
deb [signed-by=/usr/share/keyrings/debian-archive-keyring.gpg] https://deb.debian.org/debian trixie main
SOURCES
  export APT_CONFIG="$TOOL_ROOT/apt/apt.conf"
  /usr/bin/apt-get update
  /usr/bin/apt-get --download-only -y -o Debug::NoLocking=1 install php8.4-cli php8.4-mbstring php8.4-xml php8.4-sqlite3 php8.4-mysql php8.4-curl php8.4-zip php8.4-gd
  for archive in "$TOOL_ROOT/apt/archives/"*.deb; do dpkg-deb -x "$archive" "$TOOL_ROOT/php"; done
  cat > "$TOOL_ROOT/php/php.ini" <<'INI'
extension_dir=/workspace/tooling/php/usr/lib/php/20240924
extension=ctype
extension=curl
extension=dom
extension=fileinfo
extension=gd
extension=mbstring
extension=pdo
extension=pdo_sqlite
extension=pdo_mysql
extension=sqlite3
extension=tokenizer
extension=xml
extension=xmlreader
extension=xmlwriter
extension=zip
extension=phar
extension=iconv
memory_limit=512M
upload_max_filesize=5M
post_max_size=8M
date.timezone=Asia/Jakarta
INI
  cat > "$TOOL_ROOT/bin/php" <<'PHPWRAPPER'
#!/bin/sh
export PHPRC=/workspace/tooling/php/php.ini
export LD_LIBRARY_PATH="/workspace/tooling/php/usr/lib/x86_64-linux-gnu${LD_LIBRARY_PATH:+:$LD_LIBRARY_PATH}"
exec /workspace/tooling/php/usr/bin/php8.4 -c /workspace/tooling/php/php.ini "$@"
PHPWRAPPER
  chmod +x "$TOOL_ROOT/bin/php"
fi
if ! command -v composer >/dev/null 2>&1 && [[ ! -f "$TOOL_ROOT/composer.phar" ]]; then
  curl -fL --retry 2 https://raw.githubusercontent.com/composer/getcomposer.org/main/web/download/2.8.12/composer.phar -o "$TOOL_ROOT/composer.phar"
  curl -fL --retry 2 https://raw.githubusercontent.com/composer/getcomposer.org/main/web/download/2.8.12/composer.phar.sha256sum -o "$TOOL_ROOT/composer.phar.sha256sum"
  (cd "$TOOL_ROOT" && sha256sum -c composer.phar.sha256sum)
fi
