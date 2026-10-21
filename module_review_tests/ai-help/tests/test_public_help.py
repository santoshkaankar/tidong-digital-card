import json,re,unittest
from pathlib import Path
ROOT=Path(__file__).resolve().parents[1]
ARTICLES=json.loads((ROOT/'public_help/articles.json').read_text(encoding='utf-8'))

class PublicHelpSafety(unittest.TestCase):
  def test_release_gate(self):
    self.assertFalse(ARTICLES['production_ready'])
    self.assertEqual(ARTICLES['knowledge_scope'],'how_to_only')
  def test_only_allowlisted_fields(self):
    allowed={'id','module','audience','title','questions','steps','when_unavailable','evidence','release_state'}
    for a in ARTICLES['articles']:
      self.assertEqual(set(a),allowed)
      self.assertIn(a['release_state'],('safe_generic','candidate_only','published'))
      self.assertTrue(a['steps'])
      self.assertTrue(a['questions'])
  def test_distinct_ids(self):
    ids=[a['id'] for a in ARTICLES['articles']]
    self.assertEqual(len(ids),len(set(ids)))
  def test_no_sensitive_technical_patterns(self):
    data=(ROOT/'public_help/articles.json').read_text(encoding='utf-8')
    blocked=[r'(?i)\.env(?:\b|\.)',r'(?i)API[_ -]?KEY\s*[:=]',r'(?i)Bearer\s+[A-Za-z0-9._-]+',r'(?i)select\s+\*\s+from',r'(?i)database/migrations',r'(?i)/admin/[^\s"<>]+',r'(?i)\b(?:sk-[a-zA-Z0-9]{12,}|ghp_[a-zA-Z0-9]{15,})\b']
    for p in blocked:self.assertNotRegex(data,p)
  def test_no_mutating_help_actions(self):
    self.assertFalse(any('tool_call' in a or 'mutation' in a for a in ARTICLES['articles']))
  def test_private_policy_not_public(self):
    for p in (ROOT/'public_help').rglob('*'):
      self.assertNotIn('INTERNAL',p.name)
if __name__=='__main__':unittest.main()
