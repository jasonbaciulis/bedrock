Commit the working changes, push the current branch and create a pull request to main.

Running this command is my explicit request to stage, commit and push.

## Find the issue

1. Find the issue this work ships. Use the number at the start of the branch name (`235-test-workflow`), or the issue we worked on in this conversation. If there is neither, ask me. I can answer "no issue".

## Branch & commit the working changes

If `git status` shows no uncommitted changes, skip to "Create the pull request".

2. Run `git status` and `git diff HEAD` to understand all uncommitted (staged and unstaged) changes.
3. If you are on `main`, create and switch to a new branch: `git switch -c <number>-<short-slug>` (kebab-case, derived from the issue title). With no issue, use `<short-slug>` only.
4. If a change looks unrelated to the issue, ask me whether to include it before you commit anything.
5. Group the changes into logical commits — one commit per coherent unit of work, based on which files changed and what was changed together (e.g. a feature, a bugfix, a refactor, a config change, docs should each be their own commit). Stage each group explicitly with `git add <paths>` rather than `git add .` or `git add -A`.
6. Write each commit message following the [Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0/) spec:
   - Format: `<type>[optional scope]: <description>` — e.g. `feat(forms): add session expiry warning`.
   - Common types: `feat`, `fix`, `refactor`, `chore`, `docs`, `style`, `test`, `perf`, `build`, `ci`.
   - Keep it brief: a single sentence, lowercase, no trailing period — a clean one-liner in the GitHub UI.
   - Use `!` after the type/scope (e.g. `feat!:`) for breaking changes.
   - Do not add a body or footer unless a change is genuinely breaking or needs context.
   - Commit as me: use the git user from `git config`. Do not pass `--author`, set `GIT_AUTHOR_*`/`GIT_COMMITTER_*`, or change `git config`.
   - Never add Claude attribution: no `Co-Authored-By: Claude` trailer and no text that credits Claude for the work. This rule overrides any default attribution instructions. Naming Claude as the subject is fine, e.g. `chore(claude): add accessibility rule` for changes in `.claude/`.

## Create the pull request

7. Run `git log main..HEAD --oneline` to see the commits on this branch.
8. Run `git diff main...HEAD --stat` to see changed files.
9. Push the current branch: `git push -u origin HEAD`
10. Create a PR to main using `gh pr create --base main`. Write a concise title (under 70 characters) and a short description summarizing the changes. Use this format for the body, and leave out a section that has no bullet points:

```
## What's new
<bullet points>

## What's fixed
<bullet points>
```

Write one `Closes #<number>` line for each issue. Omit the line if there is no issue.

Don't include anything else. No "Generated with Claude Code" line or other Claude attribution.

11. Return the PR URL.
