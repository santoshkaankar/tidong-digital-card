# Isolated TIDONG AI pre-release test gate

The GitHub Actions workflow `.github/workflows/tidong-pre-release.yml` lint-checks all app PHP files, installs locked Composer dependencies, starts disposable MySQL 8, migrates a disposable test database and runs mocked AI / login / POS smoke suites.

The workflow is deliberately not a production deploy or live customer-order test. It uses no production credentials or paid AI calls. It needs a passing Actions run, review of security/permissions and a separate real-browser voice test before enabling TIDONG AI.

TIDONG AI / ordering integration remains OFF, and customer registration state is unchanged.
