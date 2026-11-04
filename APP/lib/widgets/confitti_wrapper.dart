import 'package:confetti/confetti.dart';
import 'package:flutter/material.dart';

class AppConfettiWrapper extends StatefulWidget {
  final Widget child;
  final bool play; // true হলে কনফেটি চলবে

  const AppConfettiWrapper({
    super.key,
    required this.child,
    this.play = false,
  });

  @override
  State<AppConfettiWrapper> createState() => _AppConfettiWrapperState();
}

class _AppConfettiWrapperState extends State<AppConfettiWrapper> {
  late ConfettiController _controller;
  bool _isPlaying = false; // এখানে নিজের flag

  @override
  void initState() {
    super.initState();
    _controller = ConfettiController(duration: const Duration(seconds: 2));

    if (widget.play) {
      _controller.play();
      _isPlaying = true;
    }
  }

  @override
  void didUpdateWidget(covariant AppConfettiWrapper oldWidget) {
    super.didUpdateWidget(oldWidget);

    if (widget.play && !_isPlaying) {
      _controller.play();
      _isPlaying = true;
    } else if (!widget.play && _isPlaying) {
      _controller.stop();
      _isPlaying = false;
    }
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Stack(
      alignment: Alignment.center,
      children: [
        widget.child,
        ConfettiWidget(
          confettiController: _controller,
          blastDirectionality: BlastDirectionality.explosive,
          colors: const [
            Colors.red,
            Colors.blue,
            Colors.green,
            Colors.orange,
            Colors.purple,
          ],
          gravity: 0.3,
          emissionFrequency: 0.05,
          numberOfParticles: 20,
        ),
      ],
    );
  }
}
