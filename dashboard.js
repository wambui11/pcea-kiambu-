const express = require('express');
const router = express.Router();
const Student = require('../models/Student');

router.get('/', async (req, res) => {
  if (!req.session.studentId) return res.redirect('/login');

  const student = await Student.findOne({ studentId: req.session.studentId });
  res.render('dashboard', { student });
});

module.exports = router;
