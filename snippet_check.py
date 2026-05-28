from pathlib import Path
from subprocess import run
from pathlib import Path
text = Path('resources/views/kasir/index.blade.php').read_text(encoding='utf-8').splitlines()
start,end = 1988,2097
code = '\n'.join(text[start-1:end])
Path('snippet.js').write_text(code, encoding='utf-8')
out = run(['node','-c','snippet.js'], capture_output=True, text=True)
print('exit', out.returncode)
print('stderr:', out.stderr)
